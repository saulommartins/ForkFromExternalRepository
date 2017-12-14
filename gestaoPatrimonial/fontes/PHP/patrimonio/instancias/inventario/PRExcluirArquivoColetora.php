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
 * Página de Processamento
 * Data de Criação: 19/12/2012

 * @author Analista:      Gelson Wolowski
 * @author Desenvolvedor: Carolina Marçal

 * @ignore

 $Id:$

 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetora.class.php';
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetoraDados.class.php';
include_once CAM_GP_PAT_MAPEAMENTO.'TPatrimonioArquivoColetoraConsistencia.class.php';

# Define o nome dos arquivos PHP
$stPrograma = "ExcluirArquivoColetora";
$pgFilt     = "FL".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";
$pgList     = "LS".$stPrograma.".php";

$stAcao = $request->get('stAcao');

Sessao::setTrataExcecao(true);
Sessao::getTransacao()->setMapeamento($obTPatrimonioExluirArquivoColetora);

switch ($stAcao) {

    case "excluir":

        $inCodigo = $_REQUEST['codigo'];

        $obTPatrimonioArquivoColetoraDados = new TPatrimonioArquivoColetoraDados;
        $obTPatrimonioArquivoColetoraDados->setDado('codigo' , $_REQUEST['codigo']);
        $obTPatrimonioArquivoColetoraDados->recuperaDadosConsistencia($rsDados);

         if ($rsDados->getNumLinhas() > 0 ) {
             foreach ($rsDados->getElementos() as $dados) {
                $obTPatrimonioArquivoColetoraConsistencia = new TPatrimonioArquivoColetoraConsistencia;
                $obTPatrimonioArquivoColetoraConsistencia->setDado('codigo' , $dados['codigo']);
                $obTPatrimonioArquivoColetoraConsistencia->setDado('num_placa' , $dados['num_placa']);

                $obErro = $obTPatrimonioArquivoColetoraConsistencia->exclusao();
             }
         }
         $obErro = $obTPatrimonioArquivoColetoraDados->exclusao();

         $obTPatrimonioArquivoColetora = new TPatrimonioArquivoColetora;
         $obTPatrimonioArquivoColetora->setDado('codigo' , $_REQUEST['codigo']);
         $obTPatrimonioArquivoColetora->setDado('nome'     , $_REQUEST['nome']);
         $obErro = $obTPatrimonioArquivoColetora->exclusao();

        SistemaLegado::alertaAviso($pgFilt.'?'.Sessao::getId()."&stAcao=".$_REQUEST['stAcao'],$inCodigo."/"."",$_REQUEST['stAcao'],"aviso", Sessao::getId(), "../");

    break;

}

Sessao::encerraExcecao();

?>
