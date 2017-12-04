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
    * Página de Formulario de Consulta programa

    * Data de Criação   : 19/09/2008

    * @author Analista      : Bruno Ferreira
    * @author Desenvolvedor : Jânio Eduardo
    * @ignore

    * $Id:

    *Casos de uso: uc-02.09.02
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GF_ORC_COMPONENTES."ISelectOrgao.class.php"         );
include_once ( CAM_GF_PPA_COMPONENTES."ITextBoxSelectOrgao.class.php"         );

include_once ( CAM_GA_CGM_COMPONENTES."IPopUpCGM.class.php" );
include_once ( CAM_GA_NORMAS_COMPONENTES."IPopUpNorma.class.php" );
include_once(CAM_GF_PPA_COMPONENTES."ITextBoxSelectPPA.class.php");
include_once '../../classes/visao/VPPAManterPrograma.class.php';
include_once '../../classes/negocio/RPPAManterPrograma.class.php';

$obNegocio = new RPPAManterPrograma();
$obVisao = new VPPAManterPrograma( $obNegocio );

$stAcao = $request->get('stAcao');
if ( empty( $stAcao ) ) {
    $stAcao = "incluir";
}

//Define o nome dos arquivos PHP
$stPrograma    = "ConsultarPrograma";
$pgFilt        = "FL".$stPrograma.".php";
$pgList        = "LS".$stPrograma.".php";
$pgForm        = "FM".$stPrograma.".php";
$pgProc        = "PR".$stPrograma.".php";
$pgOcul        = "OC".$stPrograma.".php?".Sessao::getId();
$pgJs          = "JS".$stPrograma.".php";

include_once( $pgJs );

$obHdnAcao =  new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName ('stCtrl');
$obHdnCtrl->setValue($stCtrl);

$obLabelPPA = new label;
$obLabelPPA->setRotulo('PPA');
$obLabelPPA->setTitle (' Codigo PPA');
$obLabelPPA->setValue ($_REQUEST['inPeriodo']);

//Informar código
$obLabelPrograma = new label;
$obLabelPrograma->setRotulo('Programa');
$obLabelPrograma->setName  ('inNumPrograma');
$obLabelPrograma->setTitle ('Código do programa');
$obLabelPrograma->setValue ($_REQUEST['inNumPrograma'] );

//Informar tipo do programa
$obLabelPrograma = new label;
$obLabelPrograma->setRotulo('Tipo Programa');
$obLabelPrograma->setName  ('inCodTipoPrograma');
$obLabelPrograma->setTitle ('Tipo do programa');
$obLabelPrograma->setValue ($_REQUEST['inCodTipoPrograma'] ." - ". $_REQUEST['stNomTipoPrograma']);

//Indentificação do programa
$obLabelIdPrograma = new label;
$obLabelIdPrograma->setRotulo ('Identificação do Programa');
$obLabelIdPrograma->setTitle  ('Identificação do Programa');
$obLabelIdPrograma->setName   ('inIdPrograma');
$obLabelIdPrograma->setNull   (false);
$obLabelIdPrograma->setValue  ( $_REQUEST['inIdentificacao']);

//Diagnostico do programa
$obLabelDiagnostico = new label;
$obLabelDiagnostico->setRotulo ('Diagnóstico do Programa');
$obLabelDiagnostico->setTitle  ('Diagnóstico do Programa');
$obLabelDiagnostico->setName   ('inDigPrograma');
$obLabelDiagnostico->setValue  ($_REQUEST['inDiagnostico']);

//Objetivos do programa
$obLabelObjetivo = new label;
$obLabelObjetivo->setRotulo ('Objetivos do Programa');
$obLabelObjetivo->setTitle  ('Objetivos do Programa');
$obLabelObjetivo->setName   ('inObjPrograma');
$obLabelObjetivo->setValue  ($_REQUEST['inObjetivo']);

//Diretrizes do programa
$obLabelDiretriz = new label;
$obLabelDiretriz->setRotulo ('Diretrizes do Programa');
$obLabelDiretriz->setTitle  ('Diretrizes do Programa');
$obLabelDiretriz->setName   ('inDirPrograma');
$obLabelDiretriz->setValue  ($_REQUEST['inDiretriz']);

//Informar código
$obLabelAlvo = new label;
$obLabelAlvo->setRotulo('Público-Alvo');
$obLabelAlvo->setTitle ('Público-Alvo');
$obLabelAlvo->setName  ('inPublicoAlvo');
$obLabelAlvo->setValue ($_REQUEST['inPublicoAlvo']);

$obLabelNatureza = new label;
$obLabelNatureza->setRotulo('Natureza');
$obLabelNatureza->setTitle ('Natureza');
$obLabelNatureza->setName  ('inContinuo' );
$obLabelNatureza->setValue ($_REQUEST['inContinuo']);

$obLabelDescNorma = new label;
$obLabelDescNorma->setRotulo('Norma');
$obLabelDescNorma->setTitle ('Norma');
$obLabelDescNorma->setName  ('inNomNormaVinculada');
$obLabelDescNorma->setValue ($_REQUEST['inNomNormaVinculada']);

if ($_REQUEST['inContinuo']=="Temporario") {
    $obLabelDtInicio = new label;
    $obLabelDtInicio->setRotulo('Data de Inicio');
    $obLabelDtInicio->setTitle ('Data de Inicio');
    $obLabelDtInicio->setName  ('inDtInicio');
    $obLabelDtInicio->setValue ($_REQUEST['inDtInicio']);

    $obLabelDtTermino = new label;
    $obLabelDtTermino->setRotulo('Data de Termino');
    $obLabelDtTermino->setTitle ('Data de Termino');
    $obLabelDtTermino->setName  ('inDtTermino');
    $obLabelDtTermino->setValue ($_REQUEST['inDtTermino']);
}

$obSpanData = new span;
$obSpanData->setId('spnDtPrograma');

$obSpnOrgao = new span;
$obSpnOrgao->setId('spnListaOrgao');

$obSpnServidor = new span;
$obSpnServidor->setId('spnListaServidor');

$obSpnIndice = new span;
$obSpnIndice->setId('spnListaIndice');

$obSpnOrgao->setValue($obVisao->buscaOrgao($_REQUEST,false));
$obSpnServidor->setValue($obVisao->buscaServidor($_REQUEST,false));
$obSpnIndice->setValue($obVisao->buscaIndicadores($_REQUEST,true));

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addHidden    ($obHdnAcao );
$obFormulario->addHidden    ($obHdnCtrl );
$obFormulario->addTitulo    ('Dados do programa');
$obFormulario->addComponente($obLabelPPA);
$obFormulario->addComponente($obLabelPrograma);
$obFormulario->addComponente($obLabelIdPrograma);
$obFormulario->addComponente($obLabelDiagnostico);
$obFormulario->addComponente($obLabelObjetivo);
$obFormulario->addComponente($obLabelDiretriz);
$obFormulario->addComponente($obLabelAlvo);
$obFormulario->addComponente($obLabelNatureza );

if ($_REQUEST['inContinuo']=="Temporario") {
    $obFormulario->addComponente($obLabelDtInicio);
    $obFormulario->addComponente($obLabelDtTermino);
}

if ($_REQUEST['inNomNormaVinculada']) {
    $obFormulario->addComponente($obLabelDescNorma);
}

$obFormulario->addSpan($obSpanData);
$obFormulario->addSpan($obSpnOrgao);
$obFormulario->addSpan($obSpnServidor);
$obFormulario->addSpan($obSpnIndice);

$obButtonVoltar = new Button;
$obButtonVoltar->setName  ('Voltar');
$obButtonVoltar->setValue ('Voltar');
$obButtonVoltar->obEvento->setOnClick('CancelarCL();');
$obFormulario->defineBarra( array( $obButtonVoltar), "left", "" );
$obFormulario->show();

sistemaLegado::executaFrameOculto("buscaValor('montaData');");

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
