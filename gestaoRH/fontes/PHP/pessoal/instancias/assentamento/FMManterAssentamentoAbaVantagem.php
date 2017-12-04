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
* Aba do formulário de assentamento
* Data de Criação: 11/08/2005

* @author Analista: Vandré Miguel Ramos
* @author Desenvolvedor: Diego Lemos de Souza

* @Ignore

$Revision: 30547 $
$Name$
$Author: vandre $
$Date: 2006-08-08 14:53:12 -0300 (Ter, 08 Ago 2006) $

* Casos de uso: uc-04.04.08
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';

//****************************************//
//Define COMPONENTES DO FORMULARIO
//****************************************//
$obHdnCorrecaoId = new Hidden;
$obHdnCorrecaoId->setName( "inCorrecaoId" );
$obHdnCorrecaoId->setValue( $inCorrecaoId );

$obDataInicio = new Data;
$obDataInicio->setRotulo                    ( "*Data Inicial"                                   );
$obDataInicio->setTitle                     ( "Informe a data de inicio da vantagem ."          );
$obDataInicio->setName                      ( "dtDataInicio"                                    );
$obDataInicio->setValue                     ( $dtDataInicio                                     );
$obDataInicio->setSize                      ( 10                                                );
$obDataInicio->setMaxLength                 ( 10                                                );
$obDataInicio->setInteiro                   ( false                                             );

$obDataEncerramento = new Data;
$obDataEncerramento->setRotulo              ( "Data Encerramento"                               );
$obDataEncerramento->setTitle               ( "Infomra a data de encerramento."                 );
$obDataEncerramento->setName                ( "dtDataEncerramento"                              );
$obDataEncerramento->setValue               ( $dtDataEncerramento                               );
$obDataEncerramento->setSize                ( 10                                                );
$obDataEncerramento->setMaxLength           ( 10                                                );
$obDataEncerramento->setInteiro             ( false                                             );

$obTxtQuantidadeMeses = new TextBox;
$obTxtQuantidadeMeses->setRotulo            ( "*Quantidade de Meses"                             );
$obTxtQuantidadeMeses->setTitle             ( "Informe a quantidade de meses para a correção."  );
$obTxtQuantidadeMeses->setName              ( "inQuantidadeMeses"                               );
$obTxtQuantidadeMeses->setValue             ( $inQuantidadeMeses                                );
$obTxtQuantidadeMeses->setSize              ( 6                                                 );
$obTxtQuantidadeMeses->setMaxLength         ( 3                                                 );
$obTxtQuantidadeMeses->setInteiro           ( true                                              );

$obTxtPercentualCorrecao = new Moeda;
$obTxtPercentualCorrecao->setRotulo         ( "*Percentual de Correção(%)"                       );
$obTxtPercentualCorrecao->setTitle          ( "Informe o percentual de correção."               );
$obTxtPercentualCorrecao->setName           ( "nuPercentualCorrecao"                            );
$obTxtPercentualCorrecao->setValue          ( $nuPercentualCorrecao                             );
$obTxtPercentualCorrecao->setMaxLength      ( 6                                                 );

$obBtnIncluirVantagem = new Button;
$obBtnIncluirVantagem->setName              ( "btnIncluir"                                      );
$obBtnIncluirVantagem->setValue             ( "Incluir"                                         );
$obBtnIncluirVantagem->setTipo              ( "button"                                          );
$obBtnIncluirVantagem->obEvento->setOnClick ( "buscaValor('incluirCorrecao');"                  );

$obBtnAlterarVantagem = new Button;
$obBtnAlterarVantagem->setName              ( "btnAlterar"                                      );
$obBtnAlterarVantagem->setValue             ( "Alterar"                                         );
$obBtnAlterarVantagem->setTipo              ( "button"                                          );
$obBtnAlterarVantagem->obEvento->setOnClick ( "buscaValor('alterarCorrecao');"                  );

$obBtnLimparVantagem = new Button;
$obBtnLimparVantagem->setName               ( "btnLimpar"                                       );
$obBtnLimparVantagem->setValue              ( "Limpar"                                          );
$obBtnLimparVantagem->setTipo               ( "button"                                          );
$obBtnLimparVantagem->obEvento->setOnClick  ( "limpaAssentamento();"                            );

$obSpnVantagens = new Span;
$obSpnVantagens->setId                      ( "spnCorrecoes"                                    );
