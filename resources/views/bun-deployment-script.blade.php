cd $SITE_PATH

git pull origin $BRANCH

bun install

bun --bun run build

@if(! ($isIsolated ?? false))
sudo supervisorctl restart all

@endif
echo "✅ Deployment completed successfully!"
