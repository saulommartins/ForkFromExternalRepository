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
* Página de Formulario de Inclusao/Alteracao de Afastamento
* Data de Criação   : ???

* @author Analista: ???
* @author Programador: ???

* @ignore

$Revision: 30547 $
$Name$
$Author: souzadl $
$Date: 2007-11-20 13:08:58 -0200 (Ter, 20 Nov 2007) $

Caso de uso: uc-04.04.08
**/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//

$obHdnCodNorma = new Hidden;
$obHdnCodNorma->setName( "hdninCodAssentamentoFaixaDesconto" );
$obHdnCodNorma->setValue( $hdninCodAssentamentoFaixaDesconto );

$obTxtCodigoSefip = new TextBox;
$obTxtCodigoSefip->setRotulo              ( "Código da SEFIP"                                   );
$obTxtCodigoSefip->setTitle               ( "Informe o código da SEFIP."                         );
$obTxtCodigoSefip->setName                ( "inCodSefipTxt"                                     );
$obTxtCodigoSefip->setValue               ( $inCodSefipTxt                                      );
$obTxtCodigoSefip->setSize                ( 6                                                   );
$obTxtCodigoSefip->setMaxLength           ( 3                                                   );

$obCmbCodigoSefip = new Select;
$obCmbCodigoSefip->setRotulo              ( "Código da SEFIP"           );
$obCmbCodigoSefip->setName                ( "inCodSefip"                );
$obCmbCodigoSefip->setValue               ( $inCodSefip                 );
$obCmbCodigoSefip->setStyle               ( "width: 450px"              );
$obCmbCodigoSefip->setCampoID             ( "num_sefip"                 );
$obCmbCodigoSefip->setCampoDesc           ( "descricao"                 );
$obCmbCodigoSefip->addOption              ( "", "Selecione"             );
$obCmbCodigoSefip->preencheCombo          ( $rsSefip                    );

include_once(CAM_GRH_PES_MAPEAMENTO."TPessoalRaisAfastamento.class.php");
$obTPessoalRaisAfastamento = new TPessoalRaisAfastamento();
$obTPessoalRaisAfastamento->recuperaTodos($rsRais);

$obTxtCodigoRais = new TextBox;
$obTxtCodigoRais->setRotulo              ( "Código da RAIS"                                   );
$obTxtCodigoRais->setTitle               ( "Informe o código da RAIS."                         );
$obTxtCodigoRais->setName                ( "inCodRaisTxt"                                     );
$obTxtCodigoRais->setValue               ( $inCodRais                                         );
$obTxtCodigoRais->setSize                ( 6                                                   );
$obTxtCodigoRais->setMaxLength           ( 3                                                   );

$obCmbCodigoRais = new Select;
$obCmbCodigoRais->setRotulo              ( "Código da RAIS"           );
$obCmbCodigoRais->setName                ( "inCodRais"                );
$obCmbCodigoRais->setValue               ( $inCodRais                 );
$obCmbCodigoRais->setCampoID             ( "cod_rais"                 );
$obCmbCodigoRais->setCampoDesc           ( "descricao"                 );
$obCmbCodigoRais->addOption              ( "", "Selecione"             );
$obCmbCodigoRais->setStyle               ( "width: 450px"              );
$obCmbCodigoRais->preencheCombo          ( $rsRais                    );

//Armazena o código do Intervalo para alteração
$obHdnIdIntervalo = new Hidden;
$obHdnIdIntervalo->setName( "inIdIntervalo" );
$obHdnIdIntervalo->setValue( $inIdIntervalo );

//Define objeto TEXTBOX para armazenar o VALOR  para INICIO DO INTERVALO
$obTxtInicioIntervalo = new TextBox;
$obTxtInicioIntervalo->setRotulo     ( "Início do Intervalo" );
$obTxtInicioIntervalo->setName       ( "inInicioIntervalo" );
$obTxtInicioIntervalo->setValue      ( $inInicioIntervalo  );
$obTxtInicioIntervalo->setTitle      ( "Informe o início do intervalo de dias." );
$obTxtInicioIntervalo->setMaxLength  ( 10    );
$obTxtInicioIntervalo->setInteiro    ( true  );

//Define objeto TEXTBOX para armazenar o VALOR  para FINAL DO INTERVALO
$obTxtFimIntervalo = new TextBox;
$obTxtFimIntervalo->setRotulo     ( "Fim do Intervalo" );
$obTxtFimIntervalo->setName       ( "inFimIntervalo" );
$obTxtFimIntervalo->setValue      ( $inFimIntervalo  );
$obTxtFimIntervalo->setTitle      ( "Informe o fim do intervalo de dias." );
$obTxtFimIntervalo->setMaxLength  ( 10    );
$obTxtFimIntervalo->setInteiro    ( true  );

//Define objeto TEXTBOX para armazenar o Percentual de desconto
$obTxtDesconto = new Moeda;
$obTxtDesconto->setRotulo     ( "Desconto (%)" );
$obTxtDesconto->setName       ( "flPercentualDesc" );
$obTxtDesconto->setValue      ( $flPercentualDesc  );
$obTxtDesconto->setTitle      ( "Informe o desconto referente ao intervalo." );
$obTxtDesconto->setMaxLength  ( 6     );
$obTxtDesconto->obEvento->setOnChange ( "validaDesconto(document.frm.flPercentualDesc.value, document.frm.flPercentualDesc, 'Percentual de Desconto');" );

$obBtnIncluir = new Button;
$obBtnIncluir->setName ( "btnIncluir" );
$obBtnIncluir->setValue( "Incluir" );
$obBtnIncluir->setTipo ( "button" );
$obBtnIncluir->obEvento->setOnClick ( "return IncluiFaixa();" );

$obBtnAlterar = new Button;
$obBtnAlterar->setName ( "btnAlterar" );
$obBtnAlterar->setValue( "Alterar" );
$obBtnAlterar->setTipo ( "button" );
$obBtnAlterar->obEvento->setOnClick ( "return AlteraFaixa();" );

$obBtnLimpar = new Button;
$obBtnLimpar->setName( "btnLimpar" );
$obBtnLimpar->setValue( "Limpar" );
$obBtnLimpar->setTipo( "button" );
$obBtnLimpar->obEvento->setOnClick ( "limpaAssentamento();" );

$obSpnFaixas = new Span;
$obSpnFaixas->setId ( "spnFaixas" );

$obTxtQuantidadeDias = new TextBox;
$obTxtQuantidadeDias->setRotulo     ( "Quantidade de Dias"              );
$obTxtQuantidadeDias->setName       ( "inQuantidadeDias"                );
$obTxtQuantidadeDias->setValue      ( $inQuantidadeDias                 );
$obTxtQuantidadeDias->setTitle      ( "Informe o período de dias referente ao afastamento temporário." );
$obTxtQuantidadeDias->setMaxLength  ( 10                                );
$obTxtQuantidadeDias->setInteiro    ( true                              );
