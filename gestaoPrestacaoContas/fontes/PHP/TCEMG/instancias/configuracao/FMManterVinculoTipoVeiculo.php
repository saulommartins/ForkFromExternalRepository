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
    * Titulo do arquivo : Formulário de Vínculo do Tipo de Veículo do TCM para o URBEM
    * Data de Criação   : 22/12/2008

    * @author Analista      Tonismar Regis Bernardo
    * @author Desenvolvedor Eduardo Paculski Schitz

    * @package URBEM
    * @subpackage

    * @ignore

    $Id: FMManterVinculoTipoVeiculo.php 59612 2014-09-02 12:00:51Z gelson $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");
include_once(CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php" );
include_once CAM_GPC_TCEMG_MAPEAMENTO.'TTMGTipoVeiculoTCE.class.php';
include_once CAM_GP_FRO_MAPEAMENTO."TFrotaTipoVeiculo.class.php";

$stPrograma = "ManterVinculoTipoVeiculo";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

if (empty( $stAcao )) {
    $stAcao = "alterar";
}

$stLocation = $pgList . "?". Sessao::getId() . "&stAcao=" . $stAcao;

$obForm = new Form;
$obForm->setAction( $pgProc );
$obForm->setTarget( "oculto" );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

//Lista os Tipos de Veículos cadastrados no Sistema
$obTFrotaTipoVeiculo = new TFrotaTipoVeiculo;
$obTFrotaTipoVeiculo->recuperaVinculoTipoVeiculoTCE($rsTipoVeiculo, '', 'tipo_veiculo.cod_tipo');

$obLista = new Lista();
$obLista->setMostraPaginacao(false);
$obLista->setTitulo('Lista dos Tipos de Veículos do URBEM');
$obLista->setRecordSet($rsTipoVeiculo);
//Cabeçalhos
$obLista->addCabecalho('', 5);
$obLista->addCabecalho('Tipo Veículo', 35);

//Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('[cod_tipo] - [nom_tipo]');
$obLista->commitDado();

$obTTMGTipoVeiculoTCE = new TTMGTipoVeiculoTCE();
$obTTMGTipoVeiculoTCE->recuperaTipoVeiculoTCE($rsTipoVeiculoTCE );

$obCmbTipoVeiculoTCE = new Select;
$obCmbTipoVeiculoTCE->setName       ('inCodTipo_[cod_tipo]_');
$obCmbTipoVeiculoTCE->setId         ('inCodTipo_[cod_tipo]_');
$obCmbTipoVeiculoTCE->setValue      ('[cod_tipo_tce]');
$obCmbTipoVeiculoTCE->addOption     ('', 'Selecione');
$obCmbTipoVeiculoTCE->setCampoId    ('[cod_tipo_tce]');
$obCmbTipoVeiculoTCE->setCampoDesc  ('[nom_tipo_tce]');
$obCmbTipoVeiculoTCE->setStyle      ("width: 230");
$obCmbTipoVeiculoTCE->preencheCombo ( $rsTipoVeiculoTCE );
$obCmbTipoVeiculoTCE->obEvento->setOnChange("ajaxJavaScript('".$pgOcul."?".Sessao::getId()."&stNomTipo='+this.name+'&inCodTipo='+this.value,'montaSubtipo');");

$obLista->addCabecalho('Tipo Veículo TCE', 20);
$obLista->addDadoComponente($obCmbTipoVeiculoTCE);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo("tipo_veiculo");
$obLista->commitDadoComponente();

$obCmbSubTipoVeiculoTCE = new Select();
$obCmbSubTipoVeiculoTCE->setName      ('inCodSubtipo_[cod_tipo]_');
$obCmbSubTipoVeiculoTCE->setId        ('inCodSubtipo_[cod_tipo]_');
$obCmbSubTipoVeiculoTCE->setValue     ('[cod_subtipo_tce]');
$obCmbSubTipoVeiculoTCE->addOption    ('', 'Selecione');
$obCmbSubTipoVeiculoTCE->setCampoId   ('[cod_subtipo_tce]');
$obCmbSubTipoVeiculoTCE->setCampoDesc ('[nom_subtipo_tce]');
$obCmbSubTipoVeiculoTCE->setStyle     ("width: 230" );

$obLista->addCabecalho('Subtipo Veículo TCE', 20);
$obLista->addDadoComponente($obCmbSubTipoVeiculoTCE);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo("subtipo_veiculo");
$obLista->commitDadoComponente();

$obSpnCodigos = new Span();
$obSpnCodigos->setId('spnCodigos');
$obLista->montaHTML();
$obSpnCodigos->setValue($obLista->getHTML());

//DEFINICAO DOS COMPONENTES
$obFormulario = new Formulario();
$obFormulario->addForm  ($obForm);

$obFormulario->addHidden($obHdnAcao);
$obFormulario->addHidden($obHdnCtrl);
$obFormulario->addSpan  ($obSpnCodigos);

$obFormulario->OK();
$obFormulario->show();

$jsOnload = "executaFuncaoAjax('carregaSelectsForm');";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
