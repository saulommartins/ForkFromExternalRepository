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
    * Oculto do Formulário para escolha do Plano de Contas
    * Data de Criação   : 08/10/20012

    * @author Analista      Tonismar Régis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    * @ignore
*/

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/componentes/Table/Table.class.php';
include_once CAM_GF_CONT_NEGOCIO.'RContabilidadePlanoContaGeral.class.php';

//Define o nome dos arquivos PHP
$stPrograma = "ManterEscolhaPlanoConta";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";

$stCtrl = $_REQUEST['stCtrl'];

switch ($stCtrl) {
    case 'montaVersoes':
        $obRContabilidadePlanoContaGeral = new RContabilidadePlanoContaGeral;
        $obRContabilidadePlanoContaGeral->obRAdministracaoUF->setCodigoUF($_REQUEST['cod_uf']);
        $obRContabilidadePlanoContaGeral->listarVersoes($rsVersoes);

        $obSlVersao = new Select;
        $obSlVersao->setRotulo    ('Versões');
        $obSlVersao->setTitle     ('Selecione a versão.');
        $obSlVersao->setName      ('inCodPlano');
        $obSlVersao->addOption    ('', 'Selecione');
        $obSlVersao->setCampoID   ('[cod_uf]_[cod_plano]');
        $obSlVersao->setCampoDesc ('versao');
        $obSlVersao->preencheCombo($rsVersoes);

        $obFormulario = new Formulario();
        $obFormulario->addComponente($obSlVersao);

        $obFormulario->montaInnerHTML();
        echo $obFormulario->getHTML();

        break;
}

?>
