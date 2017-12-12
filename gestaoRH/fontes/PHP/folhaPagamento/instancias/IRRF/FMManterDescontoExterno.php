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
    * Arquivo de Formulário
    * Data de Criação: 30/07/2007

    * @author Analista: Dagiane
    * @author Desenvolvedor: Tiago Finger

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30547 $
    $Name$
    $Author: souzadl $
    $Date: 2007-08-31 09:22:21 -0300 (Sex, 31 Ago 2007) $

    * Casos de uso: uc-04.05.60
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once ( CAM_GRH_PES_COMPONENTES."IFiltroContrato.class.php"                                       );
include_once ( CAM_GRH_FOL_NEGOCIO."RFolhaPagamentoPeriodoMovimentacao.class.php"                        );

//**************************************************************************************************************************//
//Define NOME ARQUIVOS
//**************************************************************************************************************************//
$stPrograma = "ManterDescontoExterno";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJS   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');

//**************************************************************************************************************************//
//Define OBJETO CONSULTAR ÚLTIMA COMPETÊNCIA
//**************************************************************************************************************************//
$obRFolhaPagamentoFolhaSituacao = new RFolhaPagamentoFolhaSituacao(new RFolhaPagamentoPeriodoMovimentacao);

//**************************************************************************************************************************//
//Define COMPONENTES DO FORMULARIO
//**************************************************************************************************************************//
$obHdnAcao =  new Hidden;
$obHdnAcao->setName                             	( "stAcao"                                                              );
$obHdnAcao->setValue                            	( $stAcao                                                               );

$obHdnCtrl =  new Hidden;
$obHdnCtrl->setName                             	( "stCtrl"                                                              );
$obHdnCtrl->setValue                            	( $stCtrl                                                               );

$obHdnTimestamp = new Hidden;
$obHdnTimestamp->setName                            ( "stTimestamp"                                                    	    );
$obHdnTimestamp->setValue                           ( $stTimestamp                                                      	);

//Instancia o form
$obForm = new Form;
$obForm->setAction      ( $pgProc  );
$obForm->setTarget      ( "oculto" );

$obIFiltroContrato = new IFiltroContrato;
$obIFiltroContrato->obIContratoDigitoVerificador->setNull ( false 															);

if ($stAcao == 'alterar') {

    $obIFiltroContrato->obIContratoDigitoVerificador->obTxtRegistroContrato->obEvento->setOnBlur("montaParametrosGET('montaValoresAlteracao', 'inContrato');");
    $obIFiltroContrato->obIContratoDigitoVerificador->setTipo ( "desconto_externo_irrf"	     								);
}

$obTxtValorBaseIRRF = new Moeda;
$obTxtValorBaseIRRF->setTitle					   ( "Informe o valor da base de IRRF calculada em outras entidades." 		);
$obTxtValorBaseIRRF->setRotulo					   ( "Valor Base do IRRF" 													);
$obTxtValorBaseIRRF->setName  					   ( "fiValorBaseIRRF" 														);
$obTxtValorBaseIRRF->setId   					   ( "fiValorBaseIRRF" 														);
$obTxtValorBaseIRRF->setValue					   ( $fiValorBaseIRRF  														);
$obTxtValorBaseIRRF->setNull					   ( false 																	);
$obTxtValorBaseIRRF->setSize					   ( 20 																	);
$obTxtValorBaseIRRF->setMaxLength				   ( 10                														);

$obTxtValorDescontoIRRF = new Moeda;
$obTxtValorDescontoIRRF->setTitle                  ( "Informe o valor do desconto de IRRF descontado em outras entidades."  );
$obTxtValorDescontoIRRF->setRotulo 				   ( "Valor desconto IRRF" 													);
$obTxtValorDescontoIRRF->setName				   ("fiValorDescontoIRRF" 													);
$obTxtValorDescontoIRRF->setId					   ("fiValorDescontoIRRF" 													);
$obTxtValorDescontoIRRF->setValue				   ( $fiValorDescontoIRRF 													);
$obTxtValorDescontoIRRF->setSize				   ( 20 				   													);
$obTxtValorDescontoIRRF->setMaxLength			   ( 10                   													);

$obDtVigencia = new Data;
$obDtVigencia->setTitle( "Informe a vigência, a partir de que data as informações deverão ser utilizadas para os cálculos dos ajustes de IRRF." );
$obDtVigencia->setRotulo							( "Vigência"	 														);
$obDtVigencia->setName								( "stDataVigencia"													 	);
$obDtVigencia->setId								( "stDataVigencia" 														);
$obDtVigencia->setValue								( $stDataVigencia  														);
$obDtVigencia->setNull								( false 																);
$obDtVigencia->obEvento->setOnChange				( "montaParametrosGET( 'verificaVigencia', 'stDataVigencia, inContrato');");

$obBtnOk = new Ok;
$obBtnOk->setName									( "btnOk" 																);
$obBtnOk->setTitle									( "Clique para gravar as informações." 									);
$obBtnOk->obEvento->setOnClick						( "montaParametrosGET('submeter', '', true);						   ");

if ($stAcao == 'alterar') {
    $obBtnOk->setDisabled( true );
}

$obBtnLimpar = new Limpar;
$obBtnLimpar->setName								( "btnLimpar" 															);
$obBtnLimpar->setTitle								( "Clique para limpar os dados dos campos." 							);

//**************************************************************************************************************************//
//Define FORMULARIO
//**************************************************************************************************************************//
$obFormulario = new Formulario;
$obFormulario->addHidden                        	( $obHdnAcao                                                            );
$obFormulario->addHidden                        	( $obHdnCtrl                                                            );
$obFormulario->addHidden							( $obHdnTimestamp 														);
$obFormulario->addTitulo 							( $obRFolhaPagamentoFolhaSituacao->consultarCompetencia() , "right" 	);
$obFormulario->addForm								( $obForm 																);

$obIFiltroContrato->setTituloFormulario				( "Desconto Externo IRRF"												);
$obIFiltroContrato->geraFormulario					( $obFormulario 														);

$obFormulario->addComponente						( $obTxtValorBaseIRRF												    );
$obFormulario->addComponente						( $obTxtValorDescontoIRRF											    );
$obFormulario->addComponente						( $obDtVigencia 													    );
$obFormulario->defineBarra  					    ( array( $obBtnOk, $obBtnLimpar ) 										);
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';

?>
