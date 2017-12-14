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
    * Página de Formulario de Exclusão programa

    * Data de Criação   : 17/11/2008

    * @author Analista      : Bruno Ferreira
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

    * $Id:

    *Casos de uso: uc-02.09.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CAM_GA_NORMAS_COMPONENTES."IPopUpNorma.class.php" );
include_once ( CAM_GF_PPA_VISAO."VPPAManterAcao.class.php");
include_once ( CAM_GF_PPA_NEGOCIO."RPPAManterAcao.class.php");

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ManterPrograma";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs          = "JS".$stPrograma.".php";

include_once( $pgJs );

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->settarget ('oculto');

$obHdnAcao =  new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao);

$obHdnCodPrograma =  new Hidden;
$obHdnCodPrograma->setName   ('inCodPrograma');
$obHdnCodPrograma->setValue  ($_REQUEST['inCodPrograma']);

$obHdnNumPrograma =  new Hidden;
$obHdnNumPrograma->setName ('inNumPrograma');
$obHdnNumPrograma->setValue($_REQUEST['inNumPrograma']);

$obHdnNumPPA =  new Hidden;
$obHdnNumPPA->setName ('inCodPPA');
$obHdnNumPPA->setValue($_REQUEST['inCodPPA']);

$obLabelPrograma = new label;
$obLabelPrograma->setRotulo('Programa');
$obLabelPrograma->setTitle ('Código do programa');
$obLabelPrograma->setValue ( $_REQUEST['inNumPrograma'].' - '.$_REQUEST['inIdentificacao']);

$obPopUpNorma = new IPopUpNorma();
$obPopUpNorma->setExibeDataNorma(true);
$obPopUpNorma->setExibeDataPublicacao(true);
$obPopUpNorma->obInnerNorma->obCampoCod->setId('inCodNorma');

$obRAcao = new RPPAManterAcao();
$obVAcao = new VPPAManterAcao($obRAcao);
#Recupera ações cadastradas para o programa
$rsAcoes = $obVAcao->recuperaListaAcoes($_REQUEST['inNumPrograma']);
$obLista = new Lista();

$obLista->setMostraPaginacao(true);
$obLista->setTitulo('Lista de Ações vinculadas a este programa');
$obLista->setRecordSet($rsAcoes);

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('&nbsp;');
$obLista->ultimoCabecalho->setWidth(5);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Código Ação');
$obLista->ultimoCabecalho->setWidth(10);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Descrição da Ação');
$obLista->ultimoCabecalho->setWidth(70);
$obLista->commitCabecalho();

$obLista->addCabecalho();
$obLista->ultimoCabecalho->addConteudo('Valor da Ação');
$obLista->ultimoCabecalho->setWidth(15);
$obLista->commitCabecalho();

# Dados
$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('cod_acao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('ESQUERDA');
$obLista->ultimoDado->setCampo('descricao');
$obLista->commitDado();

$obLista->addDado();
$obLista->ultimoDado->setAlinhamento('CENTRO');
$obLista->ultimoDado->setCampo('valor');
$obLista->commitDado();

$obFormulario = new Formulario;
$obFormulario->addForm      ($obForm);
$obFormulario->addHidden    ($obHdnAcao);
$obFormulario->addHidden    ($obHdnNumPrograma);
$obFormulario->addHidden    ($obHdnCodPrograma);
$obFormulario->addHidden    ($obHdnNumPPA);
$obFormulario->addTitulo    ('Dados para Exclusão do Programa');
$obFormulario->addComponente($obLabelPrograma);
$obPopUpNorma->geraFormulario( $obFormulario );
$obFormulario->ok();
$obFormulario->show();
$obLista->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
