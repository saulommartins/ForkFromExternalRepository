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

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once(CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracaoEntidade.class.php");
include_once(CAM_GF_ORC_COMPONENTES."ITextBoxSelectEntidadeGeral.class.php" );
include_once CAM_GPC_TCERN_MAPEAMENTO.'TTCERNTipoVeiculoTCE.class.php';
include_once CAM_GPC_TCERN_MAPEAMENTO.'TTCERNEspecieVeiculoTCE.class.php';
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
$obTFrotaTipoVeiculo = new TFrotaTipoVeiculo();
$obTFrotaTipoVeiculo->recuperaVinculoTipoVeiculo($rsTipoVeiculo, '', 'tipo_veiculo.cod_tipo');

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

$obTTCERNEspecieVeiculoTCE = new TTCERNEspecieVeiculoTCE();
$obTTCERNEspecieVeiculoTCE->recuperaTodos ($rsEspecieVeiculoTCE);

$obCmbEspecieVeiculoTCE = new Select;
$obCmbEspecieVeiculoTCE->setName       ('inCodEspecie_[cod_tipo]_');
$obCmbEspecieVeiculoTCE->setId         ('inCodEspecie_[cod_tipo]_');
$obCmbEspecieVeiculoTCE->setValue      ('[cod_especie_tce]');
$obCmbEspecieVeiculoTCE->addOption     ('', 'Selecione');
$obCmbEspecieVeiculoTCE->setCampoId    ('[cod_especie_tce]');
$obCmbEspecieVeiculoTCE->setCampoDesc  ('[nom_especie_tce]');
$obCmbEspecieVeiculoTCE->setStyle      ("width: 230");
$obCmbEspecieVeiculoTCE->preencheCombo ( $rsEspecieVeiculoTCE );

$obLista->addCabecalho('Espécie Veículo TCE', 20);
$obLista->addDadoComponente($obCmbEspecieVeiculoTCE);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo("especie_veiculo");
$obLista->commitDadoComponente();

$obTTCERNTipoVeiculoTCE = new TTCERNTipoVeiculoTCE();
$obTTCERNTipoVeiculoTCE->recuperaTipoVeiculoTCE ($rsTipoVeiculoTCE);

$obCmbTipoVeiculoTCE = new Select;
$obCmbTipoVeiculoTCE->setName       ('inCodTipo_[cod_tipo]_');
$obCmbTipoVeiculoTCE->setId         ('inCodTipo_[cod_tipo]_');
$obCmbTipoVeiculoTCE->setValue      ('[cod_tipo_tce]');
$obCmbTipoVeiculoTCE->addOption     ('', 'Selecione');
$obCmbTipoVeiculoTCE->setCampoId    ('[cod_tipo_tce]');
$obCmbTipoVeiculoTCE->setCampoDesc  ('[nom_tipo_tce]');
$obCmbTipoVeiculoTCE->setStyle      ("width: 230");
$obCmbTipoVeiculoTCE->preencheCombo ( $rsTipoVeiculoTCE );

$obLista->addCabecalho('Tipo Veículo TCE', 20);
$obLista->addDadoComponente($obCmbTipoVeiculoTCE);
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo("tipo_veiculo");
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
