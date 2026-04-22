<?php $title = 'Error Logs - Dashboard'; $active = 'error_logs'; include __DIR__ . '/../header.php'; ?>
    <style>
        .table th { background-color: #f8f9fa; border-top: none; }
    </style>

    <div class="container mt-4">
        <div class="row mb-4">
            <div class="col-12">
                <h2><i class="fas fa-exclamation-triangle me-2"></i>Error Logs</h2>
                <form method="GET" class="row g-3">
                    <input type="hidden" name="action" value="error_logs">
                    <div class="col-md-2">
                        <input type="text" class="form-control" name="token" placeholder="Token" value="<?php echo htmlspecialchars($_GET['token'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" name="channel_name" placeholder="Channel Name" value="<?php echo htmlspecialchars($_GET['channel_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="text" class="form-control" name="function_name" placeholder="Function Name" value="<?php echo htmlspecialchars($_GET['function_name'] ?? ''); ?>">
                    </div>
                    <div class="col-md-2">
                        <input type="date" class="form-control" name="created_at" value="<?php echo htmlspecialchars($_GET['created_at'] ?? ''); ?>">
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary me-2"><i class="fas fa-search"></i> Search</button>
                        <a href="?action=error_logs" class="btn btn-secondary"><i class="fas fa-times"></i> Clear</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Created At</th>
                                        <th>Token</th>
                                        <th>Error Message</th>
                                        <th>Channel Name</th>
                                        <th>Function Name</th>
                                        <th>Actions</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php if (empty($logs)): ?>
                                        <tr>
                                            <td colspan="6" class="text-center">No error logs found.</td>
                                        </tr>
                                    <?php else: ?>
                                        <?php foreach ($logs as $log): ?>
                                            <tr>
                                                <td><?php echo htmlspecialchars($log['created_at_formatted']); ?></td>
                                                <td><?php echo htmlspecialchars(substr($log['token'], 0, 20)); ?><?php if (strlen($log['token']) > 20) echo '...'; ?></td>
                                                <td><?php echo htmlspecialchars(substr($log['error_message'], 0, 50)); ?><?php if (strlen($log['error_message']) > 50) echo '...'; ?></td>
                                                <td><?php echo htmlspecialchars($log['channel_name']); ?></td>
                                                <td><?php echo htmlspecialchars($log['function_name']); ?></td>
                                                <td>
                                                    <button class="btn btn-sm btn-info" onclick="showDetails(<?php echo $log['id']; ?>)">Details</button>
                                                </td>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <?php if ($totalPages > 1): ?>
                            <nav aria-label="Error logs pagination">
                                <ul class="pagination justify-content-center mt-3">
                                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                        <li class="page-item <?php echo $i == $page ? 'active' : ''; ?>">
                                            <a class="page-link" href="?action=error_logs&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                        </li>
                                    <?php endfor; ?>
                                </ul>
                            </nav>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Details Modal -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Error Log Details</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div id="modalContent">
                        <!-- Content will be loaded here -->
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        const logs = <?php echo json_encode($logs); ?>;

        function showDetails(id) {
            const log = logs.find(l => l.id == id);
            if (log) {
                const content = `
                    <p><strong>Created At:</strong> ${log.created_at_formatted}</p>
                    <p><strong>Token:</strong> <code>${log.token}</code></p>
                    <p><strong>Error Message:</strong> ${log.error_message}</p>
                    <p><strong>Body:</strong> <pre>${JSON.stringify(JSON.parse(log.body), null, 2)}</pre></p>
                    <p><strong>Query String:</strong> <pre>${log.query_string ? JSON.stringify(JSON.parse(log.query_string), null, 2) : 'N/A'}</pre></p>
                    <p><strong>Channel Name:</strong> ${log.channel_name}</p>
                    <p><strong>Function Name:</strong> ${log.function_name}</p>
                `;
                document.getElementById('modalContent').innerHTML = content;
                const modal = new bootstrap.Modal(document.getElementById('detailsModal'));
                modal.show();
            }
        }
    </script>
<?php include __DIR__ . '/../footer.php'; ?>