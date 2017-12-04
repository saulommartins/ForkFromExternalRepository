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
  * Página de Formulario de Configuração de Orgão
  * Data de Criação: 11/03/2014

  * @author Analista:      Eduardo Paculski Schitz
  * @author Desenvolvedor: Franver Sarmento de Moraes
  *
  *
  * @ignore
  * $Id: FMManterRegistroPrecoQuantitativos.php 63282 2015-08-12 14:11:42Z michel $
  * $Date: 2015-08-12 11:11:42 -0300 (Wed, 12 Aug 2015) $
  * $Author: michel $
  * $Rev: 63282 $
  */
$obHdnQtdeFornecida = new Hidden();
$obHdnQtdeFornecida->setId('nuHdnQtdeFornecida');
$obHdnQtdeFornecida->setName('nuHdnQtdeFornecida');
$obHdnQtdeFornecida->setValue('');

$obHdnCodItem = new Hidden();
$obHdnCodItem->setId('inHdnCodItemQ');
$obHdnCodItem->setName('inHdnCodItemQ');
$obHdnCodItem->setValue('');

$obHdnIdItemQ = new Hidden();
$obHdnIdItemQ->setId('inHndIdItemQ');
$obHdnIdItemQ->setName('inHndIdItemQ');
$obHdnIdItemQ->setValue('');

$obTxtExercicioQ = new Exercicio();
$obTxtExercicioQ->setRotulo("Exercício do Orgão");
$obTxtExercicioQ->setName('stExercicioOrgaoQ');
$obTxtExercicioQ->setId('stExercicioOrgaoQ');
$obTxtExercicioQ->obEvento->setOnChange("montaParametrosGET('preencheComboOrgaoAbaQuantitativo');");
$obTxtExercicioQ->setNull(true);

$obSlcOrgao = new Select();
$obSlcOrgao->setRotulo("Orgão");
$obSlcOrgao->setId("inCodOrgaoQ");
$obSlcOrgao->setName("inCodOrgaoQ");
$obSlcOrgao->addOption("","Selecione");
$obSlcOrgao->obEvento->setOnChange("montaParametrosGET('preencheComboUnidadeAbaQuantitativo');");

$obSpnOrgao = new Span();
$obSpnOrgao->setId("spnCodUnidadeQ");

$obSpnLoteQuantitativo = new Span();
$obSpnLoteQuantitativo->setId("spnLoteQuantitativo");


$obSlcItem = new Select();
$obSlcItem->setRotulo("Número do Item");
$obSlcItem->setId("inNumItemQ");
$obSlcItem->setName("inNumItemQ");
$obSlcItem->addOption("","Selecione");
$obSlcItem->obEvento->setOnChange("montaParametrosGET('preencheComboFornecedorAbaQuantitativo');");
$obSlcItem->setStyle('width:650px');

$obSlcFornecedor = new Select();
$obSlcFornecedor->setRotulo("Fornecedor");
$obSlcFornecedor->setId("inCodFornecedorQ");
$obSlcFornecedor->setName("inCodFornecedorQ");
$obSlcFornecedor->addOption("","Selecione");
$obSlcFornecedor->obEvento->setOnChange("montaParametrosGET('preencheSpanQuantidadeFornecidaAbaQuantitativo');");

$obLblQtdePermitidaFornecedor = new Label();
$obLblQtdePermitidaFornecedor->setRotulo("Quantidade Fornecida pelo Fornecedor");
$obLblQtdePermitidaFornecedor->setTitle("Quantidade informada na aba Itens.");
$obLblQtdePermitidaFornecedor->setId("nuQtdeFornecida");
$obLblQtdePermitidaFornecedor->setName("nuQtdeFornecida");
$obLblQtdePermitidaFornecedor->setValue("0,0000");

$obLblQtdeAderidaQ = new Label();
$obLblQtdeAderidaQ->setRotulo("Quantidade Total Aderida");
$obLblQtdeAderidaQ->setTitle("Quantidade total aderida do item em todos os orgãos.");
$obLblQtdeAderidaQ->setId("nuQtdeAderidaQ");
$obLblQtdeAderidaQ->setName("nuQtdeAderidaQ");
$obLblQtdeAderidaQ->setValue("0,0000");

$obLblSaldoItemQ = new Label();
$obLblSaldoItemQ->setRotulo("Saldo");
$obLblSaldoItemQ->setTitle("Saldo do item.");
$obLblSaldoItemQ->setId("nuSaldoItemQ");
$obLblSaldoItemQ->setName("nuSaldoItemQ");
$obLblSaldoItemQ->setValue("0,0000");

$obQuantidadeOrgao = new Quantidade();
$obQuantidadeOrgao->setRotulo("Quantidade");
$obQuantidadeOrgao->setId("nuQtdeOrgao");
$obQuantidadeOrgao->setName("nuQtdeOrgao");
$obQuantidadeOrgao->setValue("0,0000");
$obQuantidadeOrgao->setSize( 23 );
$obQuantidadeOrgao->obEvento->setOnChange("montaParametrosGET('preencheSpanQuantidadeAderidaAbaQuantitativo');");

$obBtnSalvarQuantitativo = new Button;
$obBtnSalvarQuantitativo->setName  ("btnSalvarQuantitativo");
$obBtnSalvarQuantitativo->setId    ("btnSalvarQuantitativo");
$obBtnSalvarQuantitativo->setValue ("Incluir Quantitativo");
$obBtnSalvarQuantitativo->setTipo  ("button");
$obBtnSalvarQuantitativo->obEvento->setOnClick("montaParametrosGET('incluirListaQuantitativo');");

// Define Objeto Button para Limpar Quantitativos
$obBtnLimparQuantitativo = new Button;
$obBtnLimparQuantitativo->setValue( "Limpar" );
$obBtnLimparQuantitativo->obEvento->setOnClick("montaParametrosGET('limparFormOrgaoItemQuantitativos');");


$spnQuantitativoOrgao = new Span();
$spnQuantitativoOrgao->setId("spnQuantitativoOrgao");
?>