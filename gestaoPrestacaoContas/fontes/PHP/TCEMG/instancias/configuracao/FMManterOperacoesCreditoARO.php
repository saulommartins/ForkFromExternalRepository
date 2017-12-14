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
/*
    * Formulário de Cadastro das Operações de Crédito ARO
    * Data de Criação   : 10/03/2015
    * 
    * @author Analista      Dagiane Vieira
    * @author Desenvolvedor Michel Teixeira
    * 
    * @package URBEM
    * @subpackage
    * 
    * @ignore
    * 
    $Id: FMManterOperacoesCreditoARO.php 61852 2015-03-10 16:39:19Z michel $
*/


include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include_once CAM_GF_ORC_NEGOCIO.'ROrcamentoEntidade.class.php';

$stPrograma = "ManterOperacoesCreditoARO";
$pgFilt = "FL".$stPrograma.".php";
$pgList = "LS".$stPrograma.".php";
$pgForm = "FM".$stPrograma.".php";
$pgProc = "PR".$stPrograma.".php";
$pgOcul = "OC".$stPrograma.".php";
$pgJs   = "JS".$stPrograma.".js";

$stAcao = $request->get('stAcao');
$stCtrl = $_REQUEST['stCtrl'];

$obREntidade =  new ROrcamentoEntidade();
$obREntidade->obRCGM->setNumCGM( Sessao::read('numCgm') );
$obREntidade->setVerificaConfiguracao( true );
$obREntidade->listarUsuariosEntidade( $rsEntidades , " ORDER BY cod_entidade" );

$obForm = new Form;
$obForm->setAction( $pgProc );

$obHdnAcao = new Hidden;
$obHdnAcao->setName( "stAcao" );
$obHdnAcao->setValue( $stAcao );

$obHdnCtrl = new Hidden;
$obHdnCtrl->setName( "stCtrl" );
$obHdnCtrl->setValue( "" );

$obCmbEntidade = new TextBoxSelect();
$obCmbEntidade->setRotulo               ( "*Entidade"               );
$obCmbEntidade->setName                 ( "inCodEntidade"           );
$obCmbEntidade->setTitle                ( "Selecione a entidade."   );
$obCmbEntidade->setMensagem             ( "Entidade inválida"       );
$obCmbEntidade->obTextBox->setName      ( "inCodEntidade"           );
$obCmbEntidade->obTextBox->setId        ( "inCodEntidade"           );
$obCmbEntidade->obTextBox->setRotulo    ( "Entidade"                );
$obCmbEntidade->obTextBox->setTitle     ( "Selecione a Entidade"    );
$obCmbEntidade->obTextBox->setInteiro   ( true                      );
$obCmbEntidade->obTextBox->setNull      ( false                     );
$obCmbEntidade->obSelect->setName       ( "stNomEntidade"           );
$obCmbEntidade->obSelect->setId         ( "stNomEntidade"           );
$obCmbEntidade->obSelect->addOption     ( "", "Selecione"           );
$obCmbEntidade->obSelect->setCampoId    ( "cod_entidade"            );
$obCmbEntidade->obSelect->setCampoDesc  ( "nom_cgm"                 );
$obCmbEntidade->obSelect->preencheCombo ( $rsEntidades              );
$obCmbEntidade->obSelect->setNull       ( false                     );
$obCmbEntidade->obSelect->obEvento->setOnChange ("montaParametrosGET('preenchePorEntidade','inCodEntidade');");
$obCmbEntidade->obTextBox->obEvento->setOnChange("montaParametrosGET('preenchePorEntidade','inCodEntidade');");

$obDtContratacao = new Data;
$obDtContratacao->setRotulo ('Data Contratação'                 );
$obDtContratacao->setName   ('dtContratacao'                    );
$obDtContratacao->setId     ('dtContratacao'                    );
$obDtContratacao->setTitle  ('Informe a data da contratação.'   );
$obDtContratacao->setNull   ( false                             );

$obTxtVlContratado = new Moeda;
$obTxtVlContratado->setName     ( "nuVlContratado"  );
$obTxtVlContratado->setRotulo   ( "Valor Contratado");
$obTxtVlContratado->setAlign    ( 'RIGHT'           );
$obTxtVlContratado->setTitle    ( ""                );
$obTxtVlContratado->setMaxLength( 14                );
$obTxtVlContratado->setSize     ( 21                );
$obTxtVlContratado->setValue    ( ''                );
$obTxtVlContratado->setNull     ( false             );

$obDtLiquidacaoPrincipal = new Data;
$obDtLiquidacaoPrincipal->setRotulo ('Data Liquidação Principal'                    );
$obDtLiquidacaoPrincipal->setName   ('dtLiquidacaoPrincipal'                        );
$obDtLiquidacaoPrincipal->setId     ('dtLiquidacaoPrincipal'                        );
$obDtLiquidacaoPrincipal->setTitle  ('Informe a data de liquidação do principal.'   );
$obDtLiquidacaoPrincipal->setNull   ( false                                         );

$obDtLiquidacaoJuros = new Data;
$obDtLiquidacaoJuros->setRotulo ('Data Liquidação Juros'                    );
$obDtLiquidacaoJuros->setName   ('dtLiquidacaoJuros'                        );
$obDtLiquidacaoJuros->setId     ('dtLiquidacaoJuros'                        );
$obDtLiquidacaoJuros->setTitle  ('Informe a data de liquidação dos juros.'  );
$obDtLiquidacaoJuros->setNull   ( false                                     );

$obDtLiquidacaoEncargos = new Data;
$obDtLiquidacaoEncargos->setRotulo  ('Data Liquidação Encargos'                     );
$obDtLiquidacaoEncargos->setName    ('dtLiquidacaoEncargos'                         );
$obDtLiquidacaoEncargos->setId      ('dtLiquidacaoEncargos'                         );
$obDtLiquidacaoEncargos->setTitle   ('Informe a data de liquidação dos encargos.'   );
$obDtLiquidacaoEncargos->setNull    ( false                                         );

$obTxtVlLiquidacao = new Moeda;
$obTxtVlLiquidacao->setName     ( "nuVlLiquidacao"  );
$obTxtVlLiquidacao->setId       ( "nuVlLiquidacao"  );
$obTxtVlLiquidacao->setRotulo   ( "Valor Liquidação");
$obTxtVlLiquidacao->setAlign    ( 'RIGHT'           );
$obTxtVlLiquidacao->setTitle    ( ""                );
$obTxtVlLiquidacao->setMaxLength( 14                );
$obTxtVlLiquidacao->setSize     ( 21                );
$obTxtVlLiquidacao->setValue    ( ''                );
$obTxtVlLiquidacao->setNull     ( false             );

//define o formulário
$obFormulario = new Formulario;
$obFormulario->addForm  ($obForm    );
$obFormulario->addHidden($obHdnCtrl );
$obFormulario->addHidden($obHdnAcao );
$obFormulario->addTitulo    ( "Dados da Operações de Crédito ARO" );
$obFormulario->addComponente( $obCmbEntidade            );
$obFormulario->addComponente( $obDtContratacao          );
$obFormulario->addComponente( $obTxtVlContratado        );
$obFormulario->addComponente( $obDtLiquidacaoPrincipal  );
$obFormulario->addComponente( $obDtLiquidacaoJuros      );
$obFormulario->addComponente( $obDtLiquidacaoEncargos   );
$obFormulario->addComponente( $obTxtVlLiquidacao        );

$obFormulario->OK();
$obFormulario->show();

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
