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
  * Página de formulário Definir Permissão
  * Data de criação : 26/12/2005

  * @author Analista     : Diego
  * @author Desenvolvedor: Rodrigo Schreiner

  * @ignore

  * $Id:

  * Casos de uso: uc-03.03.07
**/
$stPrograma = "DefinirPermissao";
$pgFilt     = "FL".$stPrograma.".php";
$pgList     = "LS".$stPrograma.".php";
$pgForm     = "FM".$stPrograma.".php";
$pgProc     = "PR".$stPrograma.".php";
$pgOcul     = "OC".$stPrograma.".php";
$pgJs       = "JS".$stPrograma.".js";

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GP_ALM_NEGOCIO . "RAlmoxarifadoPermissaoCentroDeCustos.class.php";
include_once $pgJs;

$obRegra = new RAlmoxarifadoPermissaoCentroDeCustos();

$obForm = new Form;
$obForm->setAction ($pgProc);
$obForm->setTarget ("oculto");

//Definição dos componentes
$obHdnAcao = new Hidden;
$obHdnAcao->setName ("stAcao");
$obHdnAcao->setValue("salvar");

$obHdnStCtrl = new Hidden;
$obHdnStCtrl->setName ("stCtrl");
$obHdnStCtrl->setValue("");

$obHdnCGM = new Hidden;
$obHdnCGM->setName ("codCGM");
$obHdnCGM->setValue("");

//Define o objeto BuscaInner para CGM do Usuário
$obBscReduzido = new BuscaInner;
$obBscReduzido->setRotulo            ( "Usuário" );
$obBscReduzido->setTitle             ( "Informe o CGM do usuário" );
$obBscReduzido->setNull              ( false );
$obBscReduzido->setId                ( "stNomCGM" );
$obBscReduzido->obCampoCod->setId    ( "inNumCGM" );
$obBscReduzido->obCampoCod->setName  ( "inNumCGM" );
$obBscReduzido->obCampoCod->setAlign ("left");

// Verificar o retorno dessa requisição.
$obBscReduzido->obCampoCod->obEvento->setOnBlur( "
    new Ajax.Request('".$pgOcul."?".Sessao::getId()."',
                       { method:'get',
                         parameters: { inNumCGM: $('inNumCGM').value , stCtrl: 'buscaUsuario' } ,
                         evalJS: 'force',
                         onSuccess: function (transport) {
                            $('carregando').style.display = 'none';
                       } , onFailure: function () {
                            $('carregando').style.display = 'none';
                          }
                        }
                    );");
$obBscReduzido->obCampoCod->obEvento->setOnKeyUp( 'if (event.keyCode == 13) { montaParametrosGET(\'buscaUsuario\');}');
$obBscReduzido->obCampoCod->obEvento->setOnFocus( 'montaParametrosGET(\'buscaUsuario\');');

$obBscReduzido->setFuncaoBusca( "abrePopUp('".CAM_GA_ADM_POPUPS."usuario/FLProcurarUsuario.php','frm','inNumCGM','stNomCGM','NumCGM','".Sessao::getId()."','800','550');" );

$obSpnCentroCusto = new Span;
$obSpnCentroCusto->setId ('spnListaCentroCusto');

$obFormulario = new Formulario;
$obFormulario->addTitulo    ("Dados Para Permissão");
$obFormulario->addForm      ($obForm);
$obFormulario->setAjuda     ("UC-03.03.07");
$obFormulario->addHidden    ($obHdnAcao);
$obFormulario->addHidden    ($obHdnStCtrl);
$obFormulario->addHidden    ($obHdnCGM);
$obFormulario->addComponente($obBscReduzido);
$obFormulario->addSpan($obSpnCentroCusto);
$obFormulario->OK(true);
$obFormulario->show();

echo "<script type='text/javascript'>jQuery('#Ok').removeAttr('disabled');</script>";
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
