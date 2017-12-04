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
* Página de Formulario de filtro de desconto externo
* Data de Criação   : 30/07/2007

* @author Analista: Dagiane
* @author Desenvolvedor: Tiago Finger

* @ignore

* Casos de uso: uc-04.05.60
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_NEGOCIO."RConfiguracaoPessoal.class.php"                                     );
include_once ( CAM_GRH_PES_COMPONENTES."IContratoDigitoVerificador.class.php"                           );

//Define o nome dos arquivos PHP
$stPrograma = "ManterDescontoExterno";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

Sessao::write("stOrigem","FL");

include_once( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                      );
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//Define a função do arquivo, ex: incluir, excluir, alterar, consultar, etc
$stAcao = $request->get('stAcao');

$obRConfiguracaoPessoal = new RConfiguracaoPessoal;
$obRConfiguracaoPessoal->Consultar();
$stMascaraRegistro = $obRConfiguracaoPessoal->getMascaraRegistro();
Sessao::remove("link");

//DEFINICAO DOS COMPONENTES
$obHdnAcao =  new Hidden;
$obHdnAcao->setName   ( "stAcao" );
$obHdnAcao->setValue  ( $stAcao );

//Define o objeto de controle
$obHdnCtrl = new Hidden;
$obHdnCtrl->setName     										( "stCtrl" 											);
$obHdnCtrl->setValue    										( $stCtrl       									);

$obIContratoDigitoVerificador = new IContratoDigitoVerificador;
$obIContratoDigitoVerificador->setPagFiltro						( true 												);
$obIContratoDigitoVerificador->setTipo							( "desconto_externo_irrf"							);

//DEFINICAO DO FORM
$obForm = new Form;
$obForm->setAction												( $pgList 											);

//DEFINICAO DO FORMULARIO
$obFormulario = new Formulario;
$obFormulario->addForm              							( $obForm                           );
$obFormulario->addTitulo            							( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() ,"right" );
$obFormulario->addTitulo           								( "Filtro para Servidor"            				);
$obFormulario->addHidden            							( $obHdnAcao                        				);
$obFormulario->addHidden            							( $obHdnCtrl                        				);
$obIContratoDigitoVerificador->geraFormulario					( $obFormulario        								);
$obFormulario->ok();

$obFormulario->setFormFocus										( $obIContratoDigitoVerificador->IContratoDigitoVerificador->inContrato 					);
$obFormulario->show();

?>
