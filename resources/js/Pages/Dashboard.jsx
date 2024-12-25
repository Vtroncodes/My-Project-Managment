import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';
import { useState } from 'react';

export default function Dashboard({ auth, projects, tasks, categories }) {
    console.log({ auth, projects, tasks, categories });
    const { data, setData, post, errors } = useForm({
        projectName: '',
    });

    const [darkMode, setDarkMode] = useState(false);

    const toggleDarkMode = () => setDarkMode(!darkMode);

    const handleProjectSubmit = (e) => {
        e.preventDefault();
        post(route('projects.store'), {
            onSuccess: () => setData({ projectName: '' }),
        });
    };

    // Recursive Component for Hierarchical Categories
    const CategoriesList = ({ categories }) => {
        console.log(categories);
        if (!categories || categories.length === 0) return null;

        return (
            <ul>
                {categories.map((category) => (
                    <li key={category.id}>
                        <strong>{category.name}</strong>
                        {category.children && category.children.length > 0 && (
                            <CategoriesList categories={category.children} />
                        )}
                    </li>
                ))}
            </ul>
        );
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={
                <div className="flex justify-between items-center">
                    <h2 className={`font-semibold text-xl ${darkMode ? 'text-white' : 'text-gray-800'} leading-tight`}>
                        Dashboard
                    </h2>
                    <button
                        onClick={toggleDarkMode}
                        className={`px-4 py-2 rounded ${darkMode ? 'bg-gray-600 text-white' : 'bg-gray-200 text-gray-800'} hover:opacity-80`}
                    >
                        Toggle {darkMode ? 'Light' : 'Dark'} Mode
                    </button>
                </div>
            }
        >
            <Head title="Dashboard" />

            <div className={`py-12 ${darkMode ? 'bg-gray-900' : 'bg-gray-100'}`}>
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className={`${darkMode ? 'bg-gray-800 text-white' : 'bg-white text-gray-900'} shadow-sm sm:rounded-lg`}>
                        {/* Categories Section */}
                        <div className="p-6">
                            <h3 className="text-lg font-bold mb-4">Categories</h3>
                            <CategoriesList categories={categories} />
                        </div>

                        {/* Projects Section */}
                        <div className="p-6">
                            <h3 className="text-lg font-bold mb-4">Your Projects</h3>
                            <div className="mb-4">
                                {projects.length > 0 ? (
                                    <ul>
                                        {projects.map((project) => (
                                            <li key={project.id} className="border-b py-4">
                                                <div className="font-semibold">Project Name: {project.project_name}</div>
                                                <div>Project Description: {project.description}</div>
                                                <div>Status: {project.status}</div>
                                                <div>
                                                    Email URL:{' '}
                                                    <a href={project.email_url} className="text-blue-500 hover:underline">
                                                        {project.email_url}
                                                    </a>
                                                </div>
                                            </li>
                                        ))}
                                    </ul>
                                ) : (
                                    <p>No projects available.</p>
                                )}
                            </div>

                            <h3 className="text-lg font-bold mb-4">Create a New Project</h3>
                            <form onSubmit={handleProjectSubmit} className="mb-6">
                                <input
                                    type="text"
                                    value={data.projectName}
                                    onChange={(e) => setData('projectName', e.target.value)}
                                    placeholder="Project Name"
                                    className={`border-gray-300 rounded w-full mb-2 p-2 ${darkMode ? 'bg-gray-700 text-white' : 'bg-white text-gray-900'}`}
                                />
                                {errors.projectName && (
                                    <div className="text-red-500 text-sm">{errors.projectName}</div>
                                )}
                                <button
                                    type="submit"
                                    className={`px-4 py-2 rounded ${darkMode ? 'bg-blue-600' : 'bg-blue-500'} text-white hover:opacity-90`}
                                >
                                    Create Project
                                </button>
                            </form>
                        </div>

                        {/* Tasks Section */}
                        <div className="p-6">
                            <h3 className="text-lg font-bold mb-4">Your Tasks</h3>
                            {tasks.length > 0 ? (
                                <div className="grid grid-cols-3 gap-4">
                                    {tasks.map((task) => {
                                        const project = projects.find((proj) => proj.id === task.project_id);
                                        const projectName = project ? project.project_name : 'Unknown Project';

                                        return (
                                            <div
                                                key={task.id}
                                                className={`p-4 shadow rounded ${darkMode ? 'bg-gray-700 text-white' : 'bg-white text-gray-900'}`}
                                            >
                                                <div className="text-sm font-bold">Project: {projectName}</div>
                                                <div className="text-sm">Description: {task.description}</div>
                                                <div className="text-sm">Due Date: {task.due_date}</div>
                                                <div className="text-sm">Status: {task.status}</div>
                                                <div className="text-sm">Created At: {new Date(task.created_at).toLocaleDateString()}</div>
                                                <div className="text-sm">Updated At: {new Date(task.updated_at).toLocaleDateString()}</div>
                                            </div>
                                        );
                                    })}
                                </div>
                            ) : (
                                <p>No tasks available.</p>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
