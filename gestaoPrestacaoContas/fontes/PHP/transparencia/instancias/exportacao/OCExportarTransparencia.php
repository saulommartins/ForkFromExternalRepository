<?php
/*
    **********************************************************************************
    *                                                                                *
    * @package URBEM CNM - Soluções em Gestão Pública                                *
    * @copyright (c) 2013 Confederação Nacional de Municípos                         *
    * @author Confederação Nacional de Municípios                                    *
    *                                                                                *
    * O URBEM CNM é um software livre; você pode redistribuí-lo e/ou modificá-lo sob *
    * os  termos  da Licença Pública Geral GNU conforme  publicada  pela Fundação do *
    * Software Livre (FSF - Free Software Foundation); na versão 2 da Licença.       *
    *                                                                                *
    * Este  programa  é  distribuído  na  expectativa  de  que  seja  útil,   porém, *
    * SEM NENHUMA GARANTIA; nem mesmo a garantia implícita  de  COMERCIABILIDADE  OU *
    * ADEQUAÇÃO A UMA FINALIDADE ESPECÍFICA. Consulte a Licença Pública Geral do GNU *
    * para mais detalhes.                                                            *
    *                                                                                *
    * Você deve ter recebido uma cópia da Licença Pública Geral do GNU "LICENCA.txt" *
    * com  este  programa; se não, escreva para  a  Free  Software Foundation  Inc., *
    * no endereço 51 Franklin Street, Fifth Floor, Boston, MA 02110-1301, USA.       *
    *                                                                                *
    **********************************************************************************
*/
?>
<?php
/**
  * Página Oculta - Exportação Arquivos GPC

  * Data de Criação   : 16/11/2012

  * @author Analista: Gelson
  * @author Desenvolvedor: Carolina

  * @ignore

  $Id: OCExportarTransparencia.php 59612 2014-09-02 12:00:51Z gelson $

  * Casos de uso:
  */

set_time_limit(0);

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once CLA_EXPORTADOR;
include_once CAM_GPC_TRANSPARENCIA_MAPEAMENTO."TConfiguracaoTransparencia.class.php";
include_once CAM_GF_ORC_MAPEAMENTO."TOrcamentoEntidade.class.php";

$obTConfiguracaoTransparencia = new TConfiguracaoTransparencia();

SistemaLegado::BloqueiaFrames();

$arNomeArquivos = array(
    'ACOES.TXT',
    'BALANCETEDESPESA.TXT',
    'BALANCETERECEITA.TXT',
    'CARGOS.TXT',
    'CEDIDOSADIDOS.TXT',
    'COMPRAS.TXT',
    'CREDOR.TXT',
    'EMPENHO.TXT',
    'ENTIDADES.TXT',
    'ESTAGIARIOS.TXT',
    'FUNCOES.TXT',
    'ITEM.TXT',
    'LICITACAO.TXT',
    'LIQUIDACAO.TXT',
    'ORGAO.TXT',
    'PAGAMENTO.TXT',
    'PROGRAMA.TXT',
    'PUBLICACAOEDITAL.TXT',
    'RECURSO.TXT',
    'REMUNERACAO.TXT',
    'RUBRICA.TXT',
    'SERVIDORES.TXT',
    'SUBFUNCOES.TXT',
    'UNIDADES.TXT',
);

$arFiltroRelatorio = Sessao::read('filtroRelatorio');

# Busca todas as entidades do exercício logado para exportação dos dados.
$obTEntidade = new TOrcamentoEntidade;
$obTEntidade->setDado("exercicio", Sessao::getExercicio());
$obTEntidade->recuperaEntidades($rsEntidades, $stFiltro, $stOrdem, $boTransacao);

foreach ($rsEntidades->getElementos() as $entidade) {
    $arEntidades[] = $entidade['cod_entidade'];
}

sort($arEntidades);

$stEntidades  = implode(",",$arEntidades);
$arAnoInicial = explode('/',$arFiltroRelatorio['stDataInicial']);
$arAnoFinal   = explode('/',$arFiltroRelatorio['stDataFinal']);
$stAno = $stExercicio = Sessao::getExercicio();

$stTipoDocumento = "transparencia";
$obExportador    = new Exportador();

foreach ($arNomeArquivos as $stArquivo) {
    $arArquivo = explode( '.',$stArquivo );
    $obExportador->addArquivo($arArquivo[0].'.'.$arArquivo[1]);
    $obExportador->roUltimoArquivo->setTipoDocumento($stTipoDocumento);
    include $arArquivo[0].".inc.php";
}

# Será usado para nomear o pacote de arquivos enviados;
$now = date("Y-m-d H:i:s");

$dtLimiteDado        = SistemaLegado::dataToSql($arFiltroRelatorio['stDataFinal']);
$stHashIdentificador = SistemaLegado::pegaDado("valor", "administracao.configuracao", " WHERE exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 58 AND parametro = 'hash_identificador'");

# A cada exportação irá gerar um novo arquivo config.xml com informações do pacote.
$fp = fopen(CAM_GPC_TRANSPARENCIA_ARQUIVOS."config.xml", "w");

fwrite($fp, "<config>");
fwrite($fp, "<timestamp_geracao>".$now."</timestamp_geracao>");
fwrite($fp, "<data_limite_dado>".$dtLimiteDado."</data_limite_dado>");
fwrite($fp, "<usuario>".Sessao::getUsername()."</usuario>");
fwrite($fp, "<exercicio>".Sessao::getExercicio()."</exercicio>");
fwrite($fp, "<hash>".$stHashIdentificador."</hash>");
fwrite($fp, "<arquivos>");
fwrite($fp, "<arquivo>ACOES.TXT</arquivo>");
fwrite($fp, "<arquivo>BALANCETEDESPESA.TXT</arquivo>");
fwrite($fp, "<arquivo>BALANCETERECEITA.TXT</arquivo>");
fwrite($fp, "<arquivo>CARGOS.TXT</arquivo>");
fwrite($fp, "<arquivo>CEDIDOSADIDOS.TXT</arquivo>");
fwrite($fp, "<arquivo>COMPRAS.TXT</arquivo>");
fwrite($fp, "<arquivo>CREDOR.TXT</arquivo>");
fwrite($fp, "<arquivo>EMPENHO.TXT</arquivo>");
fwrite($fp, "<arquivo>ENTIDADES.TXT</arquivo>");
fwrite($fp, "<arquivo>ESTAGIARIOS.TXT</arquivo>");
fwrite($fp, "<arquivo>FUNCOES.TXT</arquivo>");
fwrite($fp, "<arquivo>ITEM.TXT</arquivo>");
fwrite($fp, "<arquivo>LICITACAO.TXT</arquivo>");
fwrite($fp, "<arquivo>LIQUIDACAO.TXT</arquivo>");
fwrite($fp, "<arquivo>ORGAO.TXT</arquivo>");
fwrite($fp, "<arquivo>PAGAMENTO.TXT</arquivo>");
fwrite($fp, "<arquivo>PROGRAMA.TXT</arquivo>");
fwrite($fp, "<arquivo>PUBLICACAOEDITAL.TXT</arquivo>");
fwrite($fp, "<arquivo>RECURSO.TXT</arquivo>");
fwrite($fp, "<arquivo>REMUNERACAO.TXT</arquivo>");
fwrite($fp, "<arquivo>RUBRICA.TXT</arquivo>");
fwrite($fp, "<arquivo>SERVIDORES.TXT</arquivo>");
fwrite($fp, "<arquivo>SUBFUNCOES.TXT</arquivo>");
fwrite($fp, "<arquivo>UNIDADES.TXT</arquivo>");
fwrite($fp, "</arquivos>");
fwrite($fp, "</config>");
fclose($fp);

# Arquivos serão sempre compactados para o envio ao FTP.
$obExportador->setNomeArquivoZip(date("YmdHis").'.zip');
$obExportador->show();

# Adiciona o arquivo de configuração config.xml ao pacote compactado.
$pacoteZip = Sessao::read('arArquivosDownload');

$zip = new ZipArchive;
if ($zip->open($pacoteZip[0]['stLink']) === TRUE) {
    $zip->addFile(CAM_GPC_TRANSPARENCIA_ARQUIVOS."config.xml", "config.xml");
    $zip->close();
    unlink(CAM_GPC_TRANSPARENCIA_ARQUIVOS."config.xml");
} else {
    echo 'Problemas ao adicionar o arquivo config.xml';
}

# Move o pacote para o diretório de onde é enviado ao Portal da Transparência.
if (!copy($pacoteZip[0]['stLink'], CAM_GPC_TRANSPARENCIA_ARQUIVOS.$pacoteZip[0]['stNomeArquivo'])) {
    echo "Problemas ao copiar o pacote ao diretório de arquivos.";
}

SistemaLegado::LiberaFrames();

?>