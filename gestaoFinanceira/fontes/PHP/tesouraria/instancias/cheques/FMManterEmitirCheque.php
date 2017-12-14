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
 * Formulario de Emissão de Cheques
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */

include '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/cabecalho.inc.php';
include CAM_GF_TES_CONTROLE . 'CTesourariaCheque.class.php';
include CAM_GF_TES_NEGOCIO . 'RTesourariaCheque.class.php';
include CAM_GT_MON_COMPONENTES . 'IMontaAgenciaConta.class.php';
include CAM_GF_ORC_COMPONENTES . 'ILabelEntidade.class.php';
include CAM_GF_TES_COMPONENTES . 'IMontaContaCheque.class.php';

$stAcao = $request->get('stAcao');
$pgOcul = 'OCManterEmitirCheque.php';
$pgList = 'LSManterEmitirCheque.php?stAcao=' . $stAcao;

include 'JSManterEmitirCheque.js';

Sessao::remove('arCheque');

//Instancia o model e o controller
$obModel      = new RTesourariaCheque();
$obController = new CTesourariaCheque($obModel);

//Instancia um objeto Form
$obForm = new Form();
$obForm->setAction('PRManterCheque.php');
$obForm->setTarget('oculto');

//Instancia um objeto hidden da acao
$obHdnAcao = new Hidden();
$obHdnAcao->setName    ('stAcao');
$obHdnAcao->setValue   ($stAcao );

//Instancia um hidden para o tipo de emissao
$obHdnTipoEmissao = new Hidden();
$obHdnTipoEmissao->setName    ('stTipoEmissaoCheque'           );
$obHdnTipoEmissao->setId      ('stTipoEmissaoCheque'           );
$obHdnTipoEmissao->setValue   ($request->get('stTipoEmissaoCheque'));

//Instancia um TextBox para o exercicio
$obTxtExercicio = new TextBox();
$obTxtExercicio->setName     ('stExercicio');
$obTxtExercicio->setId       ('stExercicio');
$obTxtExercicio->setRotulo   ('Exercício'  );
$obTxtExercicio->setValue    ($request->get('stExercicio'));
$obTxtExercicio->setLabel    (true         );

//Instancia o componente ILabelEntidade
$obILabelEntidade = new ILabelEntidade($obForm                   );
$obILabelEntidade->setCodEntidade     ($request->get('inCodEntidade'));
$obILabelEntidade->setExercicio       ($request->get('stExercicio')  );
$obILabelEntidade->setMostraCodigo    (true                      );

//Instancia um objeto Formulario
$obFormulario = new Formulario();
$obFormulario->addForm        ($obForm            );
$obFormulario->addHidden      ($obHdnAcao         );
$obFormulario->addHidden      ($obHdnTipoEmissao  );
switch ($request->get('stTipoEmissaoCheque')) {
case 'ordem_pagamento':
    //Instancia um TextBox para a OP
    $obTxtOrdem = new TextBox();
    $obTxtOrdem->setName     ('inCodOrdem');
    $obTxtOrdem->setId       ('inCodOrdem');
    $obTxtOrdem->setRotulo   ('Nr. da OP' );
    $obTxtOrdem->setValue    ($request->get('inCodOrdem'));
    $obTxtOrdem->setLabel    (true                   );

    //Instancia um label para o valor da OP
    $obLblValorOp = new Label();
    $obLblValorOp->setRotulo ('Valor da OP');
    $obLblValorOp->setValue  ($request->get('flValor'));

    //Instancia um label para o valor da retencao
    $obTxtValorRetencao = new TextBox();
    $obTxtValorRetencao->setRotulo ('Valor da Retenção');
    $obTxtValorRetencao->setName   ('flValorRetencao'  );
    $obTxtValorRetencao->setValue  ($request->get('flValorRetencao'));
    $obTxtValorRetencao->setLabel  (true);

    $obFormulario->addTitulo      ('Dados da Emissão por Ordem de Pagamento');

    $obFormulario->addComponente        ($obTxtExercicio    );
    $obILabelEntidade->geraFormulario   ($obFormulario      );
    $obFormulario->addComponente($obTxtOrdem      );

    break;
case 'transferencia':
    //instancia hidden para o cod_lote
    $obHdnCodLote = new Hidden();
    $obHdnCodLote->setName    ('inCodLote');
    $obHdnCodLote->setValue   ($request->get('inCodLote'));

    //instancia um hidden para o tipo
    $obHdnTipo = new Hidden();
    $obHdnTipo->setName    ('stTipo'           );
    $obHdnTipo->setValue   ($request->get('stTipo'));

     //Instancia o componente IIntervaloPopUpContaBanco
    include CAM_GF_CONT_COMPONENTES . 'IPopUpContaBanco.class.php';
    $obIPopUpContaBancoDebito = new IPopUpContaBanco();
    $obIPopUpContaBancoDebito->setName              ('stNomCodContaDebito'   );
    $obIPopUpContaBancoDebito->setId                ('stNomCodContaDebito'   );
    $obIPopUpContaBancoDebito->obCampoCod->setName  ('inCodContaDebito'      );
    $obIPopUpContaBancoDebito->setRotulo            ('Conta Débito'          );
    $obIPopUpContaBancoDebito->setTitle             ('Informe a conta débito');
    $obIPopUpContaBancoDebito->setNull              (true                    );
    $obIPopUpContaBancoDebito->setValue             ($request->get('stNomPlanoDebito'));
    $obIPopUpContaBancoDebito->obCampoCod->setValue ($request->get('inCodPlanoDebito'));
    $obIPopUpContaBancoDebito->setLabel             (true);

    $obIPopUpContaBancoCredito = new IPopUpContaBanco();
    $obIPopUpContaBancoCredito->setName              ('stNomCodContaCrdito'    );
    $obIPopUpContaBancoCredito->setId                ('stNomCodContaCrdito'    );
    $obIPopUpContaBancoCredito->obCampoCod->setName  ('inCodContaCredito'      );
    $obIPopUpContaBancoCredito->setRotulo            ('Conta Crédito'          );
    $obIPopUpContaBancoCredito->setTitle             ('Informe a conta crédito');
    $obIPopUpContaBancoCredito->setNull              (true                     );
    $obIPopUpContaBancoCredito->setValue             ($request->get('stNomPlanoCredito'));
    $obIPopUpContaBancoCredito->obCampoCod->setValue ($request->get('inCodPlanoCredito'));
    $obIPopUpContaBancoCredito->setLabel             (true);

    $obFormulario->addTitulo      ('Dados da Emissão por Transferência');

    $obFormulario->addHidden            ($obHdnCodLote             );
    $obFormulario->addHidden            ($obHdnTipo                );
    $obFormulario->addComponente        ($obTxtExercicio           );
    $obILabelEntidade->geraFormulario   ($obFormulario             );
    $obFormulario->addComponente        ($obIPopUpContaBancoCredito);
    $obFormulario->addComponente        ($obIPopUpContaBancoDebito );

    break;
case 'despesa_extra':
    //Instancia um TextBox para a OP
    $obTxtReciboExtra = new TextBox();
    $obTxtReciboExtra->setName     ('inCodReciboExtra'           );
    $obTxtReciboExtra->setId       ('inCodReciboExtra'           );
    $obTxtReciboExtra->setRotulo   ('Nr. do Recibo Extra'        );
    $obTxtReciboExtra->setValue    ($request->get('inCodReciboExtra'));
    $obTxtReciboExtra->setLabel    (true                         );

    $obFormulario->addTitulo ('Dados da Emissão por Despesa Extra');

    $obFormulario->addComponente        ($obTxtExercicio    );
    $obILabelEntidade->geraFormulario   ($obFormulario      );
    $obFormulario->addComponente        ($obTxtReciboExtra  );

    break;
}

//Instancia um textbox para o credor
$obTxtCredor = new TextBox();
$obTxtCredor->setName     ('stNomCredor'           );
$obTxtCredor->setId       ('stNomCredor'           );
$obTxtCredor->setRotulo   ('Credor'                );
$obTxtCredor->setValue    ($request->get('stNomCredor'));
$obTxtCredor->setLabel    (true                    );

if ($request->get('stTipoEmissaoCheque') == 'ordem_pagamento') {
    $flValorCheque = str_replace(',','.',str_replace('.','',$request->get('flValor')));
    $flValorCheque -= str_replace(',','.',str_replace('.','',$request->get('flValorRetencao')));
    $flValorCheque = number_format($flValorCheque,2,',','.');
} else {
    $flValorCheque = $request->get('flValor');
}

//Instancia um textbox para o valor
$obTxtValorTotal = new TextBox();
$obTxtValorTotal->setName     ('flValorTotal');
$obTxtValorTotal->setId       ('flValorTotal');
if ($request->get('stTipoEmissaoCheque') == 'ordem_pagamento') {
    $obTxtValorTotal->setRotulo   ('Valor Líquido');
} else {
    $obTxtValorTotal->setRotulo   ('Valor Total');
}
$obTxtValorTotal->setValue    ($flValorCheque);
$obTxtValorTotal->setLabel    (true          );

$obHdnDataCheque = new Hidden();
$obHdnDataCheque->setName    ('stDtCheque'             );
$obHdnDataCheque->setId      ('stDtCheque'             );
$obHdnDataCheque->setValue   ($request->get('stDataCheque'));

//Instancia um date para a data de emissao
$obDtEmissao = new Data            ();
$obDtEmissao->setName              ('stDtEmissao'              );
$obDtEmissao->setId                ('stDtEmissao'              );
$obDtEmissao->setRotulo            ('Data de Emissão'          );
$obDtEmissao->setTitle             ('Informe a data de emissão');
$obDtEmissao->obEvento->setOnChange("montaParametrosGET('verificaDataEmissao','stDtEmissao,stDtCheque');");
$obDtEmissao->setNull              (false                      );
$obDtEmissao->setValue             ($request->get('stDataCheque')  );
if ($request->get('stTipoEmissaoCheque') == 'transferencia') {
    $obDtEmissao->setLabel(true);
}

//Instancia um span para os cheques que ja foram utilizados na emissao
$obSpnChequeEmissao = new Span();
$obSpnChequeEmissao->setId    ('spnChequeEmissao');

//Instancia o componente IMontaContaCheque
$obIMontaContaCheque = new IMontaContaCheque();
$obIMontaContaCheque->setObrigatorioBarra   (true);
$obIMontaContaCheque->setTipoBusca          ('naoEmitidos');
$obIMontaContaCheque->setVinculoPlanoBanco  (true);
$obIMontaContaCheque->setCodEntidadeVinculo ($request->get('inCodEntidade'));

if ($request->get('stTipoEmissaoCheque') == 'transferencia') {
//Caso seja uma emissao por transferencia, busca os dados da conta credito
$obController->buscaContaBanco($_REQUEST);
    $stNumBanco = $obController->obModel->obRContabilidadePlanoBanco->obRMONAgencia->obRMONBanco->stNumBanco;
    $stNumAgencia = $obController->obModel->obRContabilidadePlanoBanco->obRMONAgencia->stNumAgencia;
    $stNumContaCorrente = $obController->obModel->obRContabilidadePlanoBanco->stContaCorrente;
    //Seta os dados no componente
    $obIMontaContaCheque->obIMontaAgenciaConta->obIMontaAgencia->obITextBoxSelectBanco->obTextBox->setValue($stNumBanco);
    $obIMontaContaCheque->obIMontaAgenciaConta->obIMontaAgencia->obITextBoxSelectBanco->obSelect->setValue($stNumBanco);
    $obIMontaContaCheque->obIMontaAgenciaConta->obIMontaAgencia->stNumAgencia = $stNumAgencia;
    $obIMontaContaCheque->obIMontaAgenciaConta->obBscConta->obCampoCod->setValue($stNumContaCorrente);
    $obIMontaContaCheque->obIMontaAgenciaConta->obBscConta->setLabel(true);
    $obIMontaContaCheque->obIMontaAgenciaConta->obIMontaAgencia->obTextBoxSelectAgencia->setLabel(true);
    $obIMontaContaCheque->obIMontaAgenciaConta->obIMontaAgencia->obITextBoxSelectBanco->setLabel(true);
}

//Instancia um textbox para o valor do cheque
$obValorCheque = new Numerico();
$obValorCheque->setName            ('flValorCheque'            );
$obValorCheque->setId              ('flValorCheque'            );
$obValorCheque->setRotulo          ('Valor do Cheque'          );
$obValorCheque->setTitle           ('Informe o valor do Cheque');
$obValorCheque->setObrigatorioBarra(true                       );
$obValorCheque->setNegativo        (false                      );
if ($request->get('stTipoEmissaoCheque') == 'despesa_extra') {
    $obValorCheque->setValue       ($request->get('flValor')       );
    $obValorCheque->setLabel       (true                       );
}

//Instancia um textbox para a descricao
$obTxtDescricao = new TextArea();
$obTxtDescricao->setName      ('stDescricao'                  );
$obTxtDescricao->setId        ('stDescricao'                  );
$obTxtDescricao->setRotulo    ('Descrição'                    );
$obTxtDescricao->setTitle     ('Informe a descrição do cheque');
$obTxtDescricao->setNull      (true);

//Instancia um span para a lista de cheques
$obSpnCheques = new Span();
$obSpnCheques->setId    ('spnCheque');

//Instancia um botao para incluir os cheques
$obBtnIncluir = new Button         ();
$obBtnIncluir->setValue            ('Incluir');
$obBtnIncluir->obEvento->setOnClick("montaParametrosGET('insertChequeEmissao');");

//Instancia um botao ok
$obBtnOk = new Ok();

//Instancia um botao limpar
$obBtnLimparForm = new Limpar();
$obBtnLimparForm->obEvento->setOnClick("montaParamtrosGET('limparCheques');");

//Instancia um botao para limpar os cheques
$obBtnLimpar = new Button         ();
$obBtnLimpar->setValue            ('Limpar');
$obBtnLimpar->obEvento->setOnClick('limparCheque();');

$obFormulario->addHidden            ($obHdnDataCheque   );
$obFormulario->addComponente        ($obTxtCredor       );
if ($request->get('stTipoEmissaoCheque') == 'ordem_pagamento') {
    $obFormulario->addComponente    ($obLblValorOp      );
    $obFormulario->addComponente    ($obTxtValorRetencao);
}
$obFormulario->addComponente        ($obTxtValorTotal   );
$obFormulario->addComponente        ($obDtEmissao       );
$obFormulario->addSpan              ($obSpnChequeEmissao);

$obFormulario->addTitulo            ('Dados do Cheque'  );
$obIMontaContaCheque->geraFormulario($obFormulario      );
$obFormulario->addComponente        ($obValorCheque     );
$obFormulario->addComponente        ($obTxtDescricao    );
$obFormulario->defineBarra          (array($obBtnIncluir,$obBtnLimpar));

$obFormulario->addSpan              ($obSpnCheques      );

//$obFormulario->defineBarra          (array($obBtnOk,$obBtnLimpar));
$obFormulario->Cancelar             ($pgList);

$obFormulario->show                 ();

$jsOnload = "montaParametrosGET('listChequesEmissao');";

include '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/rodape.inc.php';
