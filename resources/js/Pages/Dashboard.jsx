import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Head, useForm } from '@inertiajs/react';

export default function Dashboard({ auth }) {
    const { data, setData, post, errors } = useForm({
        projectName: '',
        taskName: '',
    });

    const handleProjectSubmit = (e) => {
        e.preventDefault();
        post(route('projects.store')); // Replace 'projects.store' with your route
    };

    const handleTaskSubmit = (e) => {
        e.preventDefault();
        post(route('tasks.store')); // Replace 'tasks.store' with your route
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
                                <button
                                    type="submit"
                                    className="bg-blue-500 text-white px-4 py-2 rounded"
                                >
                                    Create Project
                                </button>
                            </form>

                            <h3 className="text-lg font-bold mb-4">Create a New Task</h3>
                            <form onSubmit={handleTaskSubmit}>
                                <input
                                    type="text"
                                    value={data.taskName}
                                    onChange={(e) => setData('taskName', e.target.value)}
                                    placeholder="Task Name"
                                    className="border-gray-300 rounded w-full mb-2"
                                />
                                {errors.taskName && (
                                    <div className="text-red-500 text-sm">{errors.taskName}</div>
                                )}
                                <button
                                    type="submit"
                                    className="bg-green-500 text-white px-4 py-2 rounded"
                                >
                                    Create Task
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </AuthenticatedLayout>
    );
}
