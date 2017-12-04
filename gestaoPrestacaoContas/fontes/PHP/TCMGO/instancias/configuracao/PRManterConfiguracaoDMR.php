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
    * Página de Processamento Configuração de DMR
    * Data de Criação   : 12/03/2014

    * @author Analista: Eduardo Paculski Schitz
    * @author Desenvolvedor: Franver Sarmento de Moraes

    * @ignore

    * $Id: PRManterConfiguracaoDMR.php 59612 2014-09-02 12:00:51Z gelson $
    *
    * $Revision: 59612 $
    * $Author: gelson $
    * $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GPC_TGO_MAPEAMENTO."TTCMGOConfigurarArquivoDMR.class.php";

//Define o nome dos arquivos PHP
$stPrograma = "ManterConfiguracaoDMR";
$pgFilt    = "FL".$stPrograma.".php";
$pgList    = "LS".$stPrograma.".php";
$pgForm    = "FM".$stPrograma.".php";
$pgProc    = "PR".$stPrograma.".php";
$pgOcul    = "OC".$stPrograma.".php";

$rsRecordSet = new RecordSet();
$obTTCMGOConfigurarArquivoDMR = new TTCMGOConfigurarArquivoDMR();

Sessao::setTrataExcecao ( true );
Sessao::getTransacao()->setMapeamento( $obMapeamento );

switch ($_REQUEST['stAcao']) {
    default:
        foreach ($_REQUEST as $stKey=>$stValue) {
            // Adiciona a Unidade Gestora na tabela : administracao.configuracao_entidade
            if (strstr($stKey,'inTipoDecreto') && trim($stValue)!='') {
                $arCodigo = explode('_',$stKey); //Formato: inTipoDecreto_22
                $obTTCMGOConfigurarArquivoDMR->setDado('exercicio',Sessao::getExercicio());
                $obTTCMGOConfigurarArquivoDMR->setDado('cod_norma',$arCodigo[1]);
                $obTTCMGOConfigurarArquivoDMR->setDado('cod_tipo_decreto', $stValue);
                $obTTCMGOConfigurarArquivoDMR->recuperaPorChave($rsRecordSet);
                if ($rsRecordSet->eof()) {
                    $obTTCMGOConfigurarArquivoDMR->inclusao();
                } else {
                    $obTTCMGOConfigurarArquivoDMR->alteracao();
                }
            }
        }
        SistemaLegado::alertaAviso($pgForm."?".Sessao::getId()."&stAcao=$stAcao"."&modulo=$stModulo","Configuração ","incluir","incluir_n", Sessao::getId(), "../");
        break;
}

Sessao::encerraExcecao();

?>
