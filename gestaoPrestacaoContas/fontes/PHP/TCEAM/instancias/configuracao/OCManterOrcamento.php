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
    * Pagina Oculta para Formulário
    * Data de Criação   : 24/01/2007

    * @author Analista: Diego Barbosa Victoria
    * @author Desenvolvedor: Henrique Boaventura

    * @ignore

    * Casos de uso: uc-06.03.00

    $Id: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterOrcamento";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgPror = "PO".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'] ?  $_REQUEST['stCtrl'] : $_REQUEST['stCtrl'];

switch ($stCtrl) {
    case 'buscaDadosEntidade':
        if ($_REQUEST['inCodEntidade'] != '') {
            include_once(TADM."TAdministracaoConfiguracaoEntidade.class.php");
            $obTAdministracaoConfiguracaoEntidade = new TAdministracaoConfiguracaoEntidade();
            $obTAdministracaoConfiguracaoEntidade->setDado('exercicio',Sessao::getExercicio());
            $obTAdministracaoConfiguracaoEntidade->setDado('cod_entidade',$_REQUEST['inCodEntidade']);
            $obTAdministracaoConfiguracaoEntidade->setDado('cod_modulo',Sessao::read('modulo') );
            $obTAdministracaoConfiguracaoEntidade->setDado('parametro','data_aprovacao_loa');

            //recupera a data de aprovacao loa
            $obTAdministracaoConfiguracaoEntidade->recuperaPorChave( $rsDataAprovacaoLoa );

            //recupera o numero da loa
            $obTAdministracaoConfiguracaoEntidade->setDado('parametro','numero_loa');
            $obTAdministracaoConfiguracaoEntidade->recuperaPorChave( $rsNumeroLoa );

            //recupera a data do ldo
            $obTAdministracaoConfiguracaoEntidade->setDado('parametro','data_aprovacao_ldo');
            $obTAdministracaoConfiguracaoEntidade->recuperaPorChave( $rsDataAprovacaoLdo );

            //recupera o numero do ldo
            $obTAdministracaoConfiguracaoEntidade->setDado('parametro','numero_ldo');
            $obTAdministracaoConfiguracaoEntidade->recuperaPorChave( $rsNumeroLdo );

            //recupera a data do qdd
            $obTAdministracaoConfiguracaoEntidade->setDado('parametro','data_aprovacao_qdd');
            $obTAdministracaoConfiguracaoEntidade->recuperaPorChave( $rsDataAprovacaoQdd );

            //recupera o numero do qdd
            $obTAdministracaoConfiguracaoEntidade->setDado('parametro','numero_qdd');
            $obTAdministracaoConfiguracaoEntidade->recuperaPorChave( $rsNumeroQdd );

            //seta os dados no formulario
            $stJs  = "$('dtAprovacaoLOA').value = '".implode('/',array_reverse(explode('-',$rsDataAprovacaoLdo->getCampo('valor'))))."';";
            $stJs .= "$('numLeiOrcamentaria').value = '".$rsNumeroLoa->getCampo('valor')."';";
            $stJs .= "$('dtAprovacaoLDO').value = '".implode('/',array_reverse(explode('-',$rsDataAprovacaoLdo->getCampo('valor'))))."';";
            $stJs .= "$('numLDO').value = '".$rsNumeroLdo->getCampo('valor')."';";
            $stJs .= "$('dtAprovacaoQDD').value = '".implode('/',array_reverse(explode('-',$rsDataAprovacaoQdd->getCampo('valor'))))."';";
            $stJs .= "$('numQDD').value = '".$rsNumeroQdd->getCampo('valor')."';";

        } else {
            $stJs = "$('dtAprovacaoLOA').value = '';";
            $stJs .= "$('numLeiOrcamentaria').value = '';";
            $stJs .= "$('dtAprovacaoLDO').value = '';";
            $stJs .= "$('numLDO').value = '';";
            $stJs .= "$('dtAprovacaoQDD').value = '';";
            $stJs .= "$('numQDD').value = '';";
        }

        echo $stJs;

        break;
}
echo $stJs;
?>
