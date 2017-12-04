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
 * Formulario de filtro de Cheques
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GT_MON_COMPONENTES . 'IMontaAgenciaConta.class.php';
include CLA_IAPPLETTERMINAL;

$stAcao = $request->get('stAcao');

$pgOcul = 'OCManterCheque.php';

Sessao::remove('paginando');

//Instancia um objeto Form
$obForm = new Form;
$obForm->setAction('LSManterAnularEmissao.php');

//Instancia o applet
$obApplet = new IAppletTerminal( $obForm );

//Instancia um hidden para o campo tipo busca
$obHdnTipoBusca = new Hidden();
$obHdnTipoBusca->setName    ('stTipoBusca');
$obHdnTipoBusca->setValue   ('emitidos'   );

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden;
$obHdnAcao->setName ('stAcao');
$obHdnAcao->setValue($stAcao );

//Instancia os radios para o filtro do tipo de emissao
$obRdOrdemPagamento = new Radio();
$obRdOrdemPagamento->setId     ('stTipoPagamento'   );
$obRdOrdemPagamento->setName   ('stTipoPagamento'   );
$obRdOrdemPagamento->setValue  ('ordem_pagamento'   );
$obRdOrdemPagamento->setLabel  ('Ordem de Pagamento');
$obRdOrdemPagamento->setRotulo ('Tipo de Emissão'   );
$obRdOrdemPagamento->obEvento->setOnChange("ajaxJavaScript('OCManterCheque.php?stNull=true&stTipoPagamento='+this.value,'montaTipoPagamento');");

$obRdDespesaExtra = new Radio();
$obRdDespesaExtra->setId     ('stTipoPagamento'   );
$obRdDespesaExtra->setName   ('stTipoPagamento'   );
$obRdDespesaExtra->setValue  ('despesa_extra'     );
$obRdDespesaExtra->setLabel  ('Despesa Extra'     );
$obRdDespesaExtra->setRotulo ('Tipo de Emissão'   );
$obRdDespesaExtra->obEvento->setOnChange("ajaxJavaScript('OCManterCheque.php?stNull=true&stTipoPagamento='+this.value,'montaTipoPagamento');");

$obRdTransferencia = new Radio();
$obRdTransferencia->setId     ('stTipoPagamento'   );
$obRdTransferencia->setName   ('stTipoPagamento'   );
$obRdTransferencia->setValue  ('transferencia'     );
$obRdTransferencia->setLabel  ('Transferência'     );
$obRdTransferencia->setRotulo ('Tipo de Emissão'   );
$obRdTransferencia->obEvento->setOnChange("ajaxJavaScript('OCManterCheque.php?stNull=true&stTipoPagamento='+this.value,'montaTipoPagamento');");

$obRdTodos = new Radio();
$obRdTodos->setId     ('stTipoPagamento');
$obRdTodos->setName   ('stTipoPagamento');
$obRdTodos->setValue  (''           );
$obRdTodos->setLabel  ('Todos'      );
$obRdTodos->setRotulo ('Filtrar'    );
$obRdTodos->obEvento->setOnChange("ajaxJavaScript('OCManterCheque.php?stNull=true&stTipoPagamento='+this.value,'montaTipoPagamento');");
$obRdTodos->setChecked(true         );

//Instancia o componente IMontaAgenciaConta
$obIMontaAgenciaConta = new IMontaAgenciaConta();
$obIMontaAgenciaConta->obBscConta->setNull    (true);
$obIMontaAgenciaConta->boVinculoPlanoBanco = true;

//Instancia um objeto TextBox
$obTxtNumeroChequeInicial = new TextBox();
$obTxtNumeroChequeInicial->setName   ('stNumeroChequeInicial'     );
$obTxtNumeroChequeInicial->setId     ('stNumeroChequeInicial'     );
$obTxtNumeroChequeInicial->setRotulo ('Número do Cheque'          );
$obTxtNumeroChequeInicial->setTitle  ('Informe o número do cheque');
$obTxtNumeroChequeInicial->setInteiro(true                        );
$obTxtNumeroChequeInicial->setNull   (true                        );

$obLblAte = new Label();
$obLblAte->setRotulo('Até');
$obLblAte->setValue('Até');

//Instancia um objeto TextBox
$obTxtNumeroChequeFinal = new TextBox();
$obTxtNumeroChequeFinal->setName   ('stNumeroChequeFinal'       );
$obTxtNumeroChequeFinal->setId     ('stNumeroChequeFinal'       );
$obTxtNumeroChequeFinal->setRotulo ('Número do Cheque'          );
$obTxtNumeroChequeFinal->setTitle  ('Informe o número do cheque');
$obTxtNumeroChequeFinal->setInteiro(true                        );
$obTxtNumeroChequeFinal->setNull   (true                        );

//Instancia um span para o filtro de cheques emitidos
$obSpnFiltroTipoPagamento = new Span();
$obSpnFiltroTipoPagamento->setId    ('spnTipoPagamento');

//Instancia um objeto Formulario
$obFormulario = new Formulario       ();
$obFormulario->addForm               ($obForm           );
$obFormulario->addHidden             ($obApplet         );

$obFormulario->addHidden             ($obHdnAcao        );
$obFormulario->addHidden             ($obHdnTipoBusca   );

$obFormulario->addTitulo             ('Filtro do Cheque');

$obIMontaAgenciaConta->geraFormulario($obFormulario     );
$obFormulario->agrupaComponentes     (array($obTxtNumeroChequeInicial,$obLblAte,$obTxtNumeroChequeFinal));

$obFormulario->agrupaComponentes (array($obRdTodos,$obRdOrdemPagamento,$obRdDespesaExtra,$obRdTransferencia));

$obFormulario->addSpan               ($obSpnFiltroTipoPagamento);

$obFormulario->Ok                    ();
$obFormulario->show                  ();

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
?>
