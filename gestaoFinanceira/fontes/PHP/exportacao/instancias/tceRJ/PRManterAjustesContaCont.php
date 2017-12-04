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
set_time_limit(0);
/**
    * Página de Processamento dos Ajustes do Elenco de Contas do TCE-RJ
    * Data de Criação   : 27/07/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Anderson C. Konze

    * @ignore

    $Revision: 30668 $
    $Name$
    $Author: cako $
    $Date: 2006-07-28 11:18:27 -0300 (Sex, 28 Jul 2006) $

    * Casos de uso: uc-02.08.16
*/

/*
$Log$
Revision 1.1  2006/07/28 14:14:49  cako
Bug #6568#

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_EXP_MAPEAMENTO."TExportacaoTCERJAjustesContaCont.php" );

//Define o nome dos arquivos PHP
$stPrograma = "ManterAjustesContaCont";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$stAcao = $_POST["stAcao"] ? $_POST["stAcao"] : $_GET["stAcao"];

$obTExportacao = new TExportacaoTCERJAjustesContaCont();

    $obErro = new Erro;

    $obTransacao = new Transacao;
    $obTransacao->begin();
    $boTransacao = $obTransacao->getTransacao();

    $cont=0;
    foreach ($_POST as $key => $value) {
        if ( strstr( $key , "tce" ) ) {
           $arContas = explode( "_" , $key );
           if ($value <> $arContas[2]) {
               $obTExportacao->setDado("exercicio", Sessao::getExercicio() );
               $obTExportacao->setDado("cod_conta", $arContas[1]);
               $obTExportacao->setDado("cod_sequencial", $value);

               if ($arContas[2] != "") {
                    if (!$value) {
                        $obErro = $obTExportacao->exclusao( $boTransacao );
                        if($obErro->ocorreu()) break;
                        else $cont++;
                    } else {
                        $obErro = $obTExportacao->alteracao( $boTransacao );
                        if($obErro->ocorreu()) break;
                        else $cont++;
                    }
               } else {
                   $obErro = $obTExportacao->inclusao( $boTransacao );
                   if($obErro->ocorreu()) break;
                   else $cont++;
               }
            }
        }
    }
    if ($cont >= 2) $stContas = $cont." Contas ajustadas";
    if ($cont == 1) $stContas = $cont." Conta ajustada";
    if ($cont == 0) $stContas = "Nenhuma conta ajustada";

    if ( !$obErro->ocorreu() ) {
        $obErro = $obTransacao->commitAndClose();
        SistemaLegado::alertaAviso($pgForm,$stContas, "incluir", "aviso", Sessao::getId(), "../");
    } else {
        $obTransacao->rollbackAndClose();
        SistemaLegado::exibeAviso(urlencode($obErro->getDescricao()),"n_incluir","erro");
    }
?>
