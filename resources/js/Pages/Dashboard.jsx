import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';

export default function Dashboard({ auth, projects }) {
    const { data, setData, post, errors } = useForm({
        projectName: '',
        taskName: '',
    });
    // https://chatgpt.com/share/6762e23c-990c-8001-807f-a5bbd6142b02
    const handleProjectSubmit = (e) => {
        e.preventDefault();
        post(route('projects.store'), {
            onSuccess: () => {
                // Reset form fields or show success message
                setData({ projectName: '', taskName: '' });
            },
            onError: (errors) => {
                console.error(errors); // Log errors for debugging
            }
        });
    };

    return (
        <AuthenticatedLayout
            user={auth.user}
            header={<h2 className="font-semibold text-xl text-gray-800 leading-tight">Dashboard</h2>}
        >
            <Head title="Dashboard" />

            <div className="py-12">
                <div className="max-w-7xl mx-auto sm:px-6 lg:px-8">
                    <div className="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div className="p-6 text-gray-900">
                            <h3 className="text-lg font-bold mb-4">Your Projects</h3>
                            <div className="mb-4">
                                {projects && projects.length > 0 ? (
                                    <ul>
                                        {projects.map((project) => (
                                            <li key={project.id} className="border-b py-2">
                                                <div className="font-semibold">Project Name: {project.project_name}</div>
                                                <div>Project Description: {project.description}</div>
                                                <div>Status: {project.status}</div>
                                                <div>Email URL: <a href={project.email_url}>{project.email_url}</a></div>
                                            </li>
                                        ))}
                                    </ul>
                                ) : (
                                    <p>No projects available.</p>
                                )}
                            </div>

                            {/* Form for creating new project */}
                            <h3 className="text-lg font-bold mb-4">Create a New Project</h3>
                            <form onSubmit={handleProjectSubmit} className="mb-6">
                                <input
                                    type="text"
                                    value={data.projectName}
                                    onChange={(e) => setData('projectName', e.target.value)}
                                    placeholder="Project Name"
                                    className="border-gray-300 rounded w-full mb-2"
                                />
                                {errors.projectName && (
                                    <div className="text-red-500 text-sm">{errors.projectName}</div>
                                )}
                                <button type="submit" className="bg-blue-500 text-white px-4 py-2 rounded">
                                    Create Project
                                </button>
                            </form>

                            {/* Form for creating new task */}
                            {/* Similar implementation as above */}
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
