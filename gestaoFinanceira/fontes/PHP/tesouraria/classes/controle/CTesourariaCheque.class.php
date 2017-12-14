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
 * Classe de controle - Cheque
 *
 * @category    Urbem
 * @package     Tesouraria
 * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
 * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
 * $Id:$
 */
class CTesourariaCheque
{
    public $obModel;

    /**
     * Metodo construtor, seta o atributo obModel com o que vier na assinatura da funcao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $obModel Classe de Negocio
     *
     * @return void
     */
    public function __construct($obModel)
    {
        $this->obModel = $obModel;
    }

    /**
     * Metodo incluir, seta os valores na classe e executa a inclusao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluir($arParam)
    {   
        $this->obModel->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNumBanco = $arParam['inCodBancoTxt'  ];
        $this->obModel->obRMONContaCorrente->obRMONAgencia->stNumAgencia            = $arParam['stNumAgenciaTxt'];
        $this->obModel->obRMONContaCorrente->stNumeroConta                          = $arParam['stContaCorrente'];
        $this->obModel->stNumCheque                                                 = $arParam['stNumeroCheque' ];

        $obErro = $this->obModel->addCheque();
        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('FMManterCheque.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Cheque cadastrado com sucesso',$arParam['stAcao'],'aviso', Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo incluir talao, seta os valores na classe e executa a inclusao em lote
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function incluirTalao($arParam)
    {
        $this->obModel->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNumBanco = $arParam['inCodBancoTxt'        ];
        $this->obModel->obRMONContaCorrente->obRMONAgencia->stNumAgencia            = $arParam['stNumAgenciaTxt'      ];
        $this->obModel->obRMONContaCorrente->stNumeroConta                          = $arParam['stContaCorrente'      ];

        $inChequeIni = (int) $arParam['stNumeroChequeInicial'];
        for ($inChequeIni; $inChequeIni <= (int) $arParam['stNumeroChequeFinal']; $inChequeIni++) {
            $stChequeMask = str_pad($inChequeIni,strlen($arParam['stNumeroChequeInicial']),'0',STR_PAD_LEFT);
            $this->obModel->stNumCheque = $stChequeMask;
            $obErro = $this->obModel->addCheque(false,true);
            if ($obErro->ocorreu()) {
                $obErro->setDescricao('O cheque ' . $stChequeMask . ' já está cadastrado');
                break;
            }
        }

        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('FMManterTalaoCheque.php' . '?' . Sessao::getId() . '&stAcao=incluir', 'Cheque cadastrado com sucesso',$arParam['stAcao'],'aviso', Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo listar, seta os valores na classe e retorna um recordset para a acao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $rsCheque RecordSet
     * @param array  $arParam  Array de dados
     *
     * @return object $obErro
     */
    public function listar(&$rsCheque, $arParam)
    {
        $this->obModel->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNumBanco = $arParam['inCodBancoTxt'        ];
        $this->obModel->obRMONContaCorrente->obRMONAgencia->stNumAgencia            = $arParam['stNumAgenciaTxt'      ];
        $this->obModel->obRMONContaCorrente->stNumeroConta                          = $arParam['stContaCorrente'      ];
        $this->obModel->stNumChequeInicial                                          = $arParam['stNumeroChequeInicial'];
        $this->obModel->stNumChequeFinal                                            = $arParam['stNumeroChequeFinal'  ];

        $this->obModel->obREmpenhoOrdemPagamento->stExercicio                            = $arParam['stExercicio'   ];
        $this->obModel->obREmpenhoOrdemPagamento->inCodigoOrdemInicial                   = $arParam['stNumOPInicial'];
        $this->obModel->obREmpenhoOrdemPagamento->inCodigoOrdemFinal                     = $arParam['stNumOPFinal'  ];
        $this->obModel->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade' ];

        $this->obModel->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio = $arParam['stExercicio'];
        $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];
        $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaCredito = $arParam['inCodContaCredito'];
        $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaDebito = $arParam['inCodContaDebito'];

        $obErro = $this->obModel->listCheque($rsCheque,$arParam);

        return $obErro;
    }

    /**
     * Metodo excluir, seta os valores na classe e delete o cheque selecionado
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $rsCheque RecordSet
     * @param array  $arParam  Array de dados
     *
     * @return object $obErro
     */
    public function excluir($arParam)
    {
        $this->obModel->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco = $arParam['inCodBanco'  ];
        $this->obModel->obRMONContaCorrente->obRMONAgencia->inCodAgencia            = $arParam['inCodAgencia'];
        $this->obModel->obRMONContaCorrente->inCodigoConta                          = $arParam['inCodConta'  ];
        $this->obModel->stNumCheque                                                 = $arParam['stNumCheque' ];

        $obErro = $this->obModel->deleteCheque();
        if (!$obErro->ocorreu()) {
            SistemaLegado::alertaAviso('FLManterCheque.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], 'Cheque '.$arParam['stNumCheque'].' excluido com sucesso',$arParam['stAcao'],'aviso', Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo emitir, seta os valores na classe e insere a emissao para cada cheque
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return object $obErro
     */
    public function emitir($arParam)
    {
        $obErro = new Erro();
        $obTransacao = new Transacao;
        $obErro =$obTransacao->abreTransacao($boFlagTransacao, $boTransacao);
        $arCheques = Sessao::read('arCheque');

        if (count($arCheques) == 0) {
            $obErro->setDescricao('Cadastre no mínimo um cheque para a emissão');
        }

        if (!$obErro->ocorreu()) {
            switch ($arParam['stTipoEmissaoCheque']) {
            case 'ordem_pagamento':
                //Se existe valor de retencao, verifica se foi emitido(s) cheque(s) para o valor liquido
                if ($_REQUEST['flValorRetencao'] != '') {
                    foreach ($arCheques as $arCheque) {
                        $flValorTotal += $arCheque['valor'];
                    }
                    if (str_replace(',','.',str_replace('.','',$_REQUEST['flValorTotal'])) != $flValorTotal) {
                        $obErro->setDescricao('O valor total dos cheques deve ser igual ao valor líquido da OP');
                    }
                }

                //Seta os dados da op
                $this->obModel->obREmpenhoOrdemPagamento->stExercicio                            = $arParam['stExercicio'   ];
                $this->obModel->obREmpenhoOrdemPagamento->inCodigoOrdem                          = $arParam['inCodOrdem'    ];
                $this->obModel->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade' ];

                break;
            case 'transferencia':
                $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->inCodLote = $arParam['inCodLote'];
                $this->obModel->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio = $arParam['stExercicio'];
                $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];
                $this->obModel->obRTesourariaTransferencia->obRTesourariaAutenticacao->stTipo = $arParam['stTipo'];

                break;
            }
            
            $obErro = $this->obModel->obTTesourariaChequeEmissao->recuperaNow($stTimestamp, $boTransacao);
            if (!$obErro->ocorreu()) {
                //Percorre todos o array de cheques inserindo os dados nas tabelas necessarias
                foreach ($arCheques as $arCheque) {
                    if (!$obErro->ocorreu()) {
                        $this->obModel->stNumCheque                                                 = $arCheque['num_cheque'        ];
                        $this->obModel->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco = $arCheque['cod_banco'         ];
                        $this->obModel->obRMONContaCorrente->obRMONAgencia->inCodAgencia            = $arCheque['cod_agencia'       ];
                        $this->obModel->obRMONContaCorrente->inCodigoConta                          = $arCheque['cod_conta_corrente'];
                        $this->obModel->stDtEmissao                                                 = $arParam ['stDtEmissao'   ];
                        $this->obModel->flValor                                                     = $arCheque['valor'             ];
                        $this->obModel->stDescricao                                                 = $arCheque['descricao'         ];
                        $this->obModel->stTimestampEmissao                                          = $stTimestamp;
                        if ($arParam['stTipoEmissaoCheque'] == 'ordem_pagamento') {
                            $obErro = $this->obModel->emitirPorOP($boTransacao);
                        } elseif ($arParam['stTipoEmissaoCheque'] == 'despesa_extra') {
                            $obErro = $this->obModel->emitirPorReciboExtra($arParam,$boTransacao);
                        } else {
                            $obErro = $this->obModel->emitirPorTransferencia($boTransacao);
                        }
                        if ($obErro->ocorreu()) {
                            break;
                        }
                    }
                }
            }
        }

        if (!$obErro->ocorreu()) {
            $stMensagem = 'Cheque(s) emitido(s) com sucesso';
            SistemaLegado::alertaAviso('LSManterImprimirCheque.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], $stMensagem,'aviso', Sessao::getId(), "../");
            $obTransacao->fechaTransacao($boFlagTransacao, $boTransacao, $obErro, $this->obModel->obTTesourariaChequeEmissao);
        } else {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }

        return $obErro;
    }

    /**
     * Metodo que executa a anulacao da emissao de cheques
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function anular($arParam)
    {
        $this->obModel->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco = $arParam['inCodBanco'        ];
        $this->obModel->obRMONContaCorrente->obRMONAgencia->inCodAgencia            = $arParam['inCodAgencia'      ];
        $this->obModel->obRMONContaCorrente->inCodigoConta                          = $arParam['inCodContaCorrente'];
        $this->obModel->stNumCheque                                                 = $arParam['stNumCheque'       ];

        $obErro = $this->obModel->anularChequeEmissao();

        if (!$obErro->ocorreu()) {
            $stMensagem = 'Cheque anulado com sucesso';
            $stJs .= "alertaAviso('" . $stMensagem . "','form','erro','".Sessao::getId()."');";
            $stJs .= 'mudaTelaPrincipal("LSManterAnularEmissao.php?stAcao=' . $arParam['stAcao'] . '")';
            echo $stJs;
            //SistemaLegado::alertaAviso('LSManterAnularEmissao.php' . '?' . Sessao::getId() . '&stAcao='.$arParam['stAcao'], $stMensagem,'aviso', Sessao::getId(), "../");
        } else {
            sistemaLegado::exibeAviso($obErro->getDescricao(), 'n_incluir', 'erro');
        }
    }

    /**
     * Metodo getCheque, seta os valores na classe e retorna e preenche o objeto do cheque
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return object $obErro
     */
    public function getCheque(&$rsCheque, $arParam)
    {
        $this->obModel->stNumCheque                                                 = $arParam['stNumCheque' ];
        $this->obModel->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco = $arParam['inCodBanco'  ];
        $this->obModel->obRMONContaCorrente->obRMONAgencia->inCodAgencia            = $arParam['inCodAgencia'];
        $this->obModel->obRMONContaCorrente->inCodigoConta                          = $arParam['inCodConta'  ];
        $this->obModel->findChequeAnalitico($rsCheque);
    }

    /**
     * Metodo listEmitirCheque, seta os valores na classe e retorna a lista adequada
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $rsCheque RecordSet
     * @param array  $arParam  Array de dados
     *
     * @return object $obErro
     */
    public function listEmitirCheque(&$rsEmitirCheque, $arParam)
    {
        switch ($arParam['stTipoPagamento']) {
        case 'ordem_pagamento':
            $this->obModel->obREmpenhoOrdemPagamento->stExercicio                            = $arParam['stExercicio'   ];
            $this->obModel->obREmpenhoOrdemPagamento->inCodigoOrdemInicial                   = $arParam['stNumOPInicial'];
            $this->obModel->obREmpenhoOrdemPagamento->inCodigoOrdemFinal                     = $arParam['stNumOPFinal'   ];
            $this->obModel->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade' ];
            $obErro = $this->obModel->listChequeOPSaldo($rsEmitirCheque);

            break;
        case 'despesa_extra':
            $this->obModel->listChequeReciboExtraSaldo($rsEmitirCheque,$arParam);
            break;
        case 'transferencia':
            //Transferencia cod_tipo = 5
            $this->obModel->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio = $arParam['stExercicio'];
            $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];
            $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaCredito = $arParam['inCodContaCredito'];
            $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaDebito = $arParam['inCodContaDebito'];
            $this->obModel->obRTesourariaTransferencia->inCodTipoTransferencia = 5;
            $obErro = $this->obModel->listChequeTransferenciaSaldo($rsEmitirCheque);

            break;

        case 'despesa_extra':

            break;
        }

    }

    /**
     * Metodo que verifica se o intervalo dos cheques está correto
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
     public function verificaIntervalo($arParam)
     {
         if ($arParam['stNumeroChequeInicial'] != '' AND $arParam['stNumeroChequeFinal'] != '') {
             if ((int) $arParam['stNumeroChequeInicial'] > (int) $arParam['stNumeroChequeFinal']) {
                 $stJs  = 'jq("#stNumeroChequeInicial").val("");';
                 $stJs .= 'jq("#stNumeroChequeFinal").val("");';
                 $stJs .= "alertaAviso('@Interalo de cheques inválido','form','erro','".Sessao::getId()."');";
                 echo $stJs;
             }
         }
     }

    /**
     * Metodo que monta o formulario necessario para a emissao do cheque
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
     public function montaTipoPagamento($arParam)
     {
         //instancia um formulario
         $obFormulario = new Formulario();

         //Instancia o componente Exercicio
         $obExercicio = new Exercicio();
         if ($arParam['stNull'] == 'true') {
             $obExercicio->setNull(true);
         }

         //Instancia o componente ISelectMultiploEntidadeUsuario
         include CAM_GF_ORC_COMPONENTES . 'ITextBoxSelectEntidadeUsuario.class.php';
         $obITextBoxSelectEntidadeUsuario = new ITextBoxSelectEntidadeUsuario();
         if ($arParam['stNull'] != 'true') {
             $obITextBoxSelectEntidadeUsuario->setNull                           (false);
         }

         //De acordo com a requisicao, monta o formulario
         switch ($arParam['stTipoPagamento']) {
         case 'ordem_pagamento':

             //Instancia dois textbox para o intervalo de op's
             $obTxtNumOPInicial = new TextBox();
             $obTxtNumOPInicial->setName     ('stNumOPInicial');
             $obTxtNumOPInicial->setId       ('stNumOPInicial');
             $obTxtNumOPInicial->setRotulo   ('Nr. da OP');
             $obTxtNumOPInicial->setTitle    ('Informe o número da OP');
             $obTxtNumOPInicial->setInteiro  (true);

             $obTxtNumOPFinal = new TextBox();
             $obTxtNumOPFinal->setName     ('stNumOPFinal');
             $obTxtNumOPFinal->setId       ('stNumOPFinal');
             $obTxtNumOPFinal->setRotulo   ('Nr. da OP');
             $obTxtNumOPFinal->setTitle    ('Informe o número da OP');
             $obTxtNumOPFinal->setInteiro  (true);

             //Instancia um label para o intervalo de op's
             $obLblAte = new Label();
             $obLblAte->setValue('Até');

             $obFormulario->addTitulo        ('Ordem de Pagamento'             );
             $obFormulario->addComponente    ($obExercicio                     );
             $obFormulario->addComponente    ($obITextBoxSelectEntidadeUsuario );
             $obFormulario->agrupaComponentes(array($obTxtNumOPInicial,$obLblAte,$obTxtNumOPFinal));

             break;
         case 'despesa_extra':
             include CAM_GF_CONT_COMPONENTES . 'IPopUpContaAnalitica.class.php';
             ///Data Emissão
             $obTextData = new Data();
             $obTextData->setName  ('stDataEmissao'             );
             $obTextData->setId    ('stDataEmissao'             );
             $obTextData->setRotulo('Data Emissão'              );
             $obTextData->setTitle ('Informe a data de emissão.');

             /// número do recibo
             $obNumeroRecibo = new TextBox();
             $obNumeroRecibo->setID       ('stNumRecibo'             );
             $obNumeroRecibo->setName     ('stNumRecibo'             );
             $obNumeroRecibo->setRotulo   ('Número do Recibo'            );
             $obNumeroRecibo->setTitle    ('Informe o número do recibo.' );

             /// busca de conta Despesa
             $obPopUpContaDespesa = new IPopUpContaAnalitica($obITextBoxSelectEntidadeUsuario->obSelect);
             $obPopUpContaDespesa->setID                    ('stNomContaDespesa'          );
             $obPopUpContaDespesa->setName                  ('stNomContaDespesa'          );
             $obPopUpContaDespesa->obCampoCod->setName      ('inCodContaDespesa'          );
             $obPopUpContaDespesa->obCampoCod->setId        ('inCodContaDespesa'          );
             $obPopUpContaDespesa->setRotulo                ('Conta de Despesa'           );
             $obPopUpContaDespesa->setTitle                 ('Informe a conta de despesa.');

             $obFormulario->addTitulo    ('Despesa Extra');
             $obFormulario->addComponente($obExercicio                    );
             $obFormulario->addComponente($obITextBoxSelectEntidadeUsuario);
             $obFormulario->addComponente($obTextData                     );
             $obFormulario->addComponente($obNumeroRecibo                 );
             $obFormulario->addComponente($obPopUpContaDespesa            );

             break;
         case 'transferencia':
             //Instancia o componente IIntervaloPopUpContaBanco
             include CAM_GF_CONT_COMPONENTES . 'IPopUpContaBanco.class.php';
             $obIPopUpContaBancoDebito = new IPopUpContaBanco();
             $obIPopUpContaBancoDebito->setName              ('stNomCodContaDebito'   );
             $obIPopUpContaBancoDebito->setId                ('stNomCodContaDebito'   );
             $obIPopUpContaBancoDebito->obCampoCod->setName  ('inCodContaDebito'      );
             $obIPopUpContaBancoDebito->setRotulo            ('Conta Débito'          );
             $obIPopUpContaBancoDebito->setTitle             ('Informe a conta débito');
             $obIPopUpContaBancoDebito->setNull              (true                    );

             $obIPopUpContaBancoCredito = new IPopUpContaBanco();
             $obIPopUpContaBancoCredito->setName              ('stNomCodContaCrdito'    );
             $obIPopUpContaBancoCredito->setId                ('stNomCodContaCrdito'    );
             $obIPopUpContaBancoCredito->obCampoCod->setName  ('inCodContaCredito'      );
             $obIPopUpContaBancoCredito->setRotulo            ('Conta Crédito'          );
             $obIPopUpContaBancoCredito->setTitle             ('Informe a conta crédito');
             $obIPopUpContaBancoCredito->setNull              (true                     );

             $obFormulario->addTitulo    ('Transferência');
             $obFormulario->addComponente($obExercicio                    );
             $obFormulario->addComponente($obITextBoxSelectEntidadeUsuario);
             $obFormulario->addComponente($obIPopUpContaBancoDebito       );
             $obFormulario->addComponente($obIPopUpContaBancoCredito      );

             break;
         }

         $obFormulario->montaInnerHTML();
         $stJs = "jq('#spnTipoPagamento').html('" . $obFormulario->getHTML() . "');";

         echo $stJs;
     }

    /**
     * Metodo que monta a lista de cheques para a emissao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arCheque Array de cheques
     *
     * @return char
     */
    public function buildListaChequeEmissao($arCheque, $stTitle, $stAction = '')
    {
        include_once CAM_FW_COMPONENTES . 'Table/Table.class.php';

        if (!is_object($arCheque)) {
            $rsCheque = new RecordSet;
            $rsCheque->preenche( $arCheque );
        } else {
            $rsCheque = $arCheque;
        }
        if ($rsCheque->getNumLinhas() > 0) {
            $rsCheque->addFormatacao('valor','NUMERIC_BR');

            $table = new Table();
            $table->setRecordset( $rsCheque );
            $table->setSummary($stTitle);

            ////$table->setConditional( true , "#efefef" );

            $table->Head->addCabecalho( 'Banco',          25);
            $table->Head->addCabecalho( 'Agência',        25);
            $table->Head->addCabecalho( 'Conta Corrente', 10);
            $table->Head->addCabecalho( 'Nr. Cheque',     10);
            $table->Head->addCabecalho( 'Valor',           8);

            $table->Body->addCampo('[num_banco] - [nom_banco]',     'E');
            $table->Body->addCampo('[num_agencia] - [nom_agencia]', 'E');
            $table->Body->addCampo('num_conta_corrente',            'E');
            $table->Body->addCampo('num_cheque',                    'E');
            $table->Body->addCampo('valor',                         'D');

            if ($stAction != '') {
                $stFunctionJs  = "ajaxJavaScript('OCManterEmitirCheque.php?cod_banco=%s&cod_agencia=%s";

                switch ($stAction) {
                case 'excluir':
                    $stTableAction = 'excluir';
                    $stFunctionJs .= "&cod_conta_corrente=%s&num_cheque=%s','deleteChequeEmissao')";
                    $table->Body->addAcao($stTableAction, $stFunctionJs, array( 'cod_banco', 'cod_agencia', 'cod_conta_corrente', 'num_cheque' ) );

                    break;
                case 'imprimir':
                    $stTableAction = 'imprimir';
                    $stFunctionJs .= "&cod_terminal=" . Sessao::read('inCodTerminal');
                    $stFunctionJs .= "&timestamp_terminal=" . Sessao::read('stTimestampTerminal');
                    $stFunctionJs .= "&cod_conta_corrente=%s&num_cheque=%s','imprimirCheque');";
                    $stFunctionJs = "confirmPopUp(
                                        'Imprimir Cheque',
                                        'Certifique-se de que a folha de cheque esteja inserida na impressora. <br/><br/>Deseja prosseguir com a impressão?',
                                        '" . addslashes($stFunctionJs) . "');";
                    $table->Body->addAcao($stTableAction, $stFunctionJs, array( 'cod_banco', 'cod_agencia', 'cod_conta_corrente', 'num_cheque' ) );

                    break;
                }

            }

            $table->Foot->addSoma('valor','D');

            $table->montaHTML();

            $stHTML = $table->getHtml();
            if ($stAction != 'imprimir') {
                $stHTML = str_replace( "\n" ,"" ,$stHTML );
                $stHTML = str_replace( "  " ,"" ,$stHTML );
                $stHTML = str_replace( "'","\\'",$stHTML );
            }
        }

        return $stHTML;
    }

    /**
     * Metodo que insere um cheque na lista de emissao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function insertChequeEmissao($arParam)
    {
        $obErro = new Erro();
        //Faz as verificacoes necessarias
        $flValorCheque = (float) str_replace(',','.',str_replace('.','',$_REQUEST['flValorCheque']));
        if ($_REQUEST['inCodBancoTxt'] == '') {
            $obErro->setDescricao('Selecione o Banco');
        } elseif ($_REQUEST['stNumAgenciaTxt'] == '') {
            $obErro->setDescricao('Selecione a Agência');
        } elseif ($_REQUEST['stContaCorrente'] == '') {
            $obErro->setDescricao('Informe a Conta Corrente');
        } elseif ($_REQUEST['stNumCheque'] == '') {
            $obErro->setDescricao('Informe o número do Cheque');
        } elseif ($flValorCheque == '0') {
            $obErro->setDescricao('O valor do cheque deve ser maior que zero');
        }

        //Monta uma chave para verificar se nao existe ja o cheque a ser incluido
        $stNewCheque = $_REQUEST['inCodBancoTxt'].$_REQUEST['stNumAgenciaTxt'].$_REQUEST['stContaCorrente'].$_REQUEST['stNumCheque'];
        $arCheques = Sessao::read('arCheque');

        if (is_array($arCheques)) {
            foreach ($arCheques as $arCheque) {
                $stCheque = $arCheque['num_banco'].$arCheque['num_agencia'].$arCheque['num_conta_corrente'].$arCheque['num_cheque'];
                $flValorTotalCheques += $arCheque['valor'];
                if ($stNewCheque === $stCheque) {
                    $obErro->setDescricao('Este cheque já está na lista');
                    break;
                }
            }
        }
        $flValorTotalCheques += $flValorCheque;

        if (!$obErro->ocorreu()) {
            $flValorTotal = str_replace(',','.',str_replace('.','',$_REQUEST['flValorTotal']));
            if ($flValorTotalCheques > $flValorTotal) {
                $obErro->setDescricao('A soma do valor dos cheques é maior do que o valor a pagar');
            }
        }

        if (!$obErro->ocorreu()) {
            //Busca os dados no banco
            $this->obModel->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNumBanco = $_REQUEST['inCodBancoTxt'  ];
            $this->obModel->obRMONContaCorrente->obRMONAgencia->stNumAgencia            = $_REQUEST['stNumAgenciaTxt'];
            $this->obModel->obRMONContaCorrente->stNumeroConta                          = $_REQUEST['stContaCorrente'];
            $this->obModel->stNumCheque                                                 = $_REQUEST['stNumCheque'    ];
            $this->obModel->listCheque($rsCheque);

            $arChequeAux = array();
            $arChequeAux['cod_banco'         ] = $rsCheque->getCampo('cod_banco'         );
            $arChequeAux['num_banco'         ] = $rsCheque->getCampo('num_banco'         );
            $arChequeAux['nom_banco'         ] = $rsCheque->getCampo('nom_banco'         );
            $arChequeAux['cod_agencia'       ] = $rsCheque->getCampo('cod_agencia'       );
            $arChequeAux['num_agencia'       ] = $rsCheque->getCampo('num_agencia'       );
            $arChequeAux['nom_agencia'       ] = $rsCheque->getCampo('nom_agencia'       );
            $arChequeAux['cod_conta_corrente'] = $rsCheque->getCampo('cod_conta_corrente');
            $arChequeAux['num_conta_corrente'] = $rsCheque->getCampo('num_conta_corrente');
            $arChequeAux['num_cheque'        ] = $rsCheque->getCampo('num_cheque'        );
            $arChequeAux['valor'             ] = $flValorCheque;
            $arChequeAux['descricao'         ] = $arParam['stDescricao'                  ];

            $arCheques[] = $arChequeAux;

            Sessao::write('arCheque',$arCheques);

            $stJs .= "jq('#spnCheque').html('" . $this->buildListaChequeEmissao($arCheques,'Lista de Cheques','excluir') . "');";
            $stJs .= "limparCheque();";

        } else {
            $stJs .= "alertaAviso('" . $obErro->getDescricao() . "','frm','erro','".Sessao::getId()."');";
        }

        echo $stJs;
    }

    /**
     * Metodo que deleta um cheque da lista de emissao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function deleteChequeEmissao($arParam)
    {
        $arCheques = Sessao::read('arCheque');
        $stKeyDel = $arParam['cod_banco'].$arParam['cod_agencia'].$arParam['cod_conta_corrente'].$arParam['num_cheque'];
        foreach ($arCheques as $arCheque) {
            $stKey = $arCheque['cod_banco'].$arCheque['cod_agencia'].$arCheque['cod_conta_corrente'].$arCheque['num_cheque'];
            if ($stKey != $stKeyDel) {
                $arChequeNew[] = $arCheque;
            }
        }

        if (!is_array($arChequeNew)) {
            $arChequeNew = array();
        }

        Sessao::write('arCheque',$arChequeNew);

        $stJs .= "jq('#spnCheque').html('" . $this->buildListaChequeEmissao($arChequeNew,'Lista de Cheques','excluir') . "');";

        echo $stJs;
    }

    /**
     * Metodo que verifica a data de emissao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function verificaDataEmissao($arParam)
    {
        $stJs = "jq('#stDtEmissao').val('" . $arParam['stDtCheque'] . "');";
        if (implode('',array_reverse(explode('/',$arParam['stDtEmissao']))) < implode('',array_reverse(explode('/',$arParam['stDtCheque'])))) {
            $stJs .= "alertaAviso('A data de emissão inválida','frm','erro','".Sessao::getId()."');";
        } else {
            $stJs = '';
        }

        echo $stJs;
    }

    /**
     * Metodo que monta a lista de cheques utilizados por emissao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function listChequesEmissao($arParam)
    {
        switch ($arParam['stTipoEmissaoCheque']) {
        case 'ordem_pagamento':
            $this->obModel->obREmpenhoOrdemPagamento->inCodigoOrdem                           = $arParam['inCodOrdem' ];
            $this->obModel->obREmpenhoOrdemPagamento->stExercicio                             = $arParam['stExercicio'];
            $this->obModel->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade  = $arParam['inCodEntidade'];
            $this->obModel->listChequesOP($rsCheque);

            break;
        case 'despesa_extra':
            $this->obModel->listChequesReciboExtra($rsCheque, $arParam);

            break;
        case 'transferencia':
            $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->inCodLote = $arParam['inCodLote'];
            $this->obModel->obRTesourariaTransferencia->roRTesourariaBoletim->setExercicio = $arParam['stExercicio'];
            $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];
            $this->obModel->obRTesourariaTransferencia->obRTesourariaAutenticacao->stTipo = $arParam['stTipo'];
            $this->obModel->listChequesTransferencia($rsCheque);

            break;
        }
        $stJs .= "jq('#spnChequeEmissao').html('" . $this->buildListaChequeEmissao($rsCheque, 'Lista de cheques já utilizados nesta emissão') . "');";
        echo $stJs;
    }

    /**
     * Metodo que monta a lista de cheques utilizados por emissao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $rsCheque RecordSet
     *
     * @return object $obErro
     */
    public function listChequesAnular(&$rsCheque,$arParam)
    {
            $this->obModel->obREmpenhoOrdemPagamento->stExercicio                            = $arParam['stExercicio'   ];
            $this->obModel->obREmpenhoOrdemPagamento->inCodigoOrdemInicial                   = $arParam['stNumOPInicial'];
            $this->obModel->obREmpenhoOrdemPagamento->inCodigoOrdemFinal                     = $arParam['stNumOPFinal'   ];
            $this->obModel->obREmpenhoOrdemPagamento->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade' ];

            $this->obModel->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio = $arParam['stExercicio'];
            $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];
            $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaCredito = $arParam['inCodContaCredito'];
            $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaDebito = $arParam['inCodContaDebito'];
            $this->obModel->obRTesourariaTransferencia->inCodTipoTransferencia = 5;
            $this->obModel->stNumChequeInicial = $arParam['stNumeroChequeInicial'];
            $this->obModel->stNumChequeFinal = $arParam['stNumeroChequeFinal'];
            $this->obModel->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNumBanco = $arParam['inCodBanco'];
            $this->obModel->obRMONContaCorrente->obRMONAgencia->stNumAgencia = $arParam['stNumAgencia'];
            $this->obModel->obRMONContaCorrente->stNumeroConta = $arParam['stContaCorrente'];

            $obErro = $this->obModel->listCheque($rsCheque, $arParam);

            return $obErro;
    }

    /**
     * Metodo que monta a lista de cheques utilizados por na baixa
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param object $rsCheque RecordSet
     *
     * @return object $obErro
     */
    public function listChequesBaixa(&$rsCheque,$arParam)
    {

            $this->obModel->obRMONContaCorrente->obRMONAgencia->obRMONBanco->stNumBanco                 = $_REQUEST['inCodBancoTxt'   ];
            $this->obModel->obRMONContaCorrente->obRMONAgencia->stNumAgencia                            = $_REQUEST['stNumAgenciaTxt' ];
            $this->obModel->obRMONContaCorrente->stNumeroConta                                          = $_REQUEST['stContaCorrente' ];
            $this->obModel->stNumCheque                                                                 = $_REQUEST['stNumCheque'     ];

            $this->obModel->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio               = $arParam['stExercicio'      ];
            $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];
            $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaCredito = $arParam['inCodContaCredito'];
            $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->inContaDebito  = $arParam['inCodContaDebito' ];

            $obErro = $this->obModel->listChequesTransferenciaBaixa($arParam, $rsCheque);

            return $obErro;
    }

    /**
     * Metodo que imprime a face do cheque
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function imprimirCheque($arParam)
    {
        $this->obModel->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco               = $arParam['cod_banco'         ];
        $this->obModel->obRMONContaCorrente->obRMONAgencia->inCodAgencia                          = $arParam['cod_agencia'       ];
        $this->obModel->obRMONContaCorrente->inCodigoConta                                        = $arParam['cod_conta_corrente'];
        $this->obModel->stNumCheque                                                               = $arParam['num_cheque'        ];
        $this->obModel->obRTesourariaImpressoraCheque->obRTesourariaTerminal->inCodTerminal       = $arParam['cod_terminal'      ];
        $this->obModel->obRTesourariaImpressoraCheque->obRTesourariaTerminal->stTimestampTerminal = $arParam['timestamp_terminal'];
        $this->obModel->findChequeEmissao();

        $this->obModel->obRTesourariaImpressoraCheque->printCheque($this->obModel);
        $stJs  = "ajaxJavaScript('OCManterEmitirCheque.php?cod_banco=" . $arParam['cod_banco'] . "&cod_agencia=" . $arParam['cod_agencia'];
        $stJs .= "&cod_terminal=" . $arParam['cod_terminal'];
        $stJs .= "&timestamp_terminal=" . $arParam['timestamp_terminal'];
        $stJs .= "&cod_conta_corrente=" . $arParam['cod_conta_corrente'] . "&num_cheque=" . $arParam['num_cheque'] . "','imprimirChequeVerso');";
        $stJs = "confirmPopUp(
                            'Imprimir Verso do Cheque',
                            'Certifique-se de que a folha de cheque esteja inserida na impressora com o verso voltado para cima. <br/><br/>Deseja prosseguir com a impressão do verso do cheque?',
                            '" . addslashes($stJs) . "');";

        echo $stJs;
    }

    /**
     * Metodo que imprime o verso do cheque
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function imprimirChequeVerso($arParam)
    {
        $this->obModel->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco               = $arParam['cod_banco'         ];
        $this->obModel->obRMONContaCorrente->obRMONAgencia->inCodAgencia                          = $arParam['cod_agencia'       ];
        $this->obModel->obRMONContaCorrente->inCodigoConta                                        = $arParam['cod_conta_corrente'];
        $this->obModel->stNumCheque                                                               = $arParam['num_cheque'        ];
        $this->obModel->obRTesourariaImpressoraCheque->obRTesourariaTerminal->inCodTerminal       = $arParam['cod_terminal'      ];
        $this->obModel->obRTesourariaImpressoraCheque->obRTesourariaTerminal->stTimestampTerminal = $arParam['timestamp_terminal'];
        $this->obModel->findChequeEmissao();

        $this->obModel->obRTesourariaImpressoraCheque->printChequeVerso($this->obModel);
        $stJs = "removeConfirmPopUp();";

        echo $stJs;
    }

    /**
     * Metodo que monta o formulario para o filtro de cheques
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function buildFiltroTipoPagamento($arParam)
    {
        //instancia um formulario
        $obFormulario = new Formulario();

        if ($arParam['stTipoBusca'] == 'emitidos') {
            //Instancia os radios para o tipo baixado
            $obRdTodos = new Radio();
            $obRdTodos->setName   ('stBaixado');
            $obRdTodos->setRotulo ('Baixado'  );
            $obRdTodos->setLabel  ('Todos'    );
            $obRdTodos->setValue  ('todos'    );
            $obRdTodos->setChecked(true       );

            $obRdSim = new Radio();
            $obRdSim->setName   ('stBaixado');
            $obRdSim->setRotulo ('Baixado'  );
            $obRdSim->setLabel  ('Sim'      );
            $obRdSim->setValue  ('sim'      );

            $obRdNao = new Radio();
            $obRdNao->setName   ('stBaixado');
            $obRdNao->setRotulo ('Baixado'  );
            $obRdNao->setLabel  ('Não'      );
            $obRdNao->setValue  ('nao'      );

            //Instancia um select para o tipo de pagamento
            $obCmbTipoPagamento = new Select();
            $obCmbTipoPagamento->setName    ('stTipoPagamento'                      );
            $obCmbTipoPagamento->setId      ('stTipoPagamento'                      );
            $obCmbTipoPagamento->setRotulo  ('Tipo de Pagamento'                    );
            $obCmbTipoPagamento->setTitle   ('Informe o tipo de pagamento'          );
            $obCmbTipoPagamento->addOption  ('', 'Selecione'                        );
            $obCmbTipoPagamento->addOption  ('ordem_pagamento', 'Ordem de Pagamento');
            $obCmbTipoPagamento->addOption  ('despesa_extra'  , 'Despesa Extra'     );
            $obCmbTipoPagamento->addOption  ('transferencia'  , 'Transferência'     );
            $obCmbTipoPagamento->setNull    (true                                  );
            //$obCmbTipoPagamento->obEvento->setOnChange("montaParametrosGET('montaTipoPagamento','stTipoPagamento');");
            $obCmbTipoPagamento->obEvento->setOnChange("ajaxJavaScript('OCManterCheque.php?stTipoPagamento='+this.value+'&stNull=true','montaTipoPagamento');");

            //Instancia um span para os dados do pagamento
            $obSpnTipoPagamento = new Span();
            $obSpnTipoPagamento->setId    ('spnTipoPagamento');

            $obFormulario->agrupaComponentes(array($obRdTodos, $obRdSim, $obRdNao));
            $obFormulario->addComponente    ($obCmbTipoPagamento);
            $obFormulario->addSpan          ($obSpnTipoPagamento);
        }

        $obFormulario->montaInnerHTML();
        $stJs = "jq('#spnFiltroTipoPagamento').html('" . $obFormulario->getHTML() . "');";

        echo $stJs;
    }

    /**
     * Metodo que limpa o form e remove os dados da sessao
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function limparCheques()
    {
        Sessao::remove('arCheque');
        $stJs .= 'limparCheques();';
        echo $stJs;
    }

    /**
     * Metodo que busca os dados de uma conta de banco
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function buscaContaBanco($arParam)
    {
        $this->obModel->obRContabilidadePlanoBanco->inCodPlano  = $arParam['inCodPlanoCredito'];
        $this->obModel->obRContabilidadePlanoBanco->stExercicio = $arParam['stExercicio'      ];
        $this->obModel->obRContabilidadePlanoBanco->obROrcamentoEntidade->inCodigoEntidade = $arParam['inCodEntidade'];
        $obErro = $this->obModel->searchContaBanco();

        return $obErro;
    }

    /**
     * Metodo que confirma a baixa das transferencias
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function confirmBaixarTransferencia($arParam)
    {
        //Percorre todo os parametros procurando pelas transferencias
        $arTransferencia = array();
        foreach ($arParam as $stKey=>$stValue) {
            if (strstr($stKey,'transferencia')) {
                $arTransferencia[] = explode($stKey,'_');
            }
        }

        //Verifica se foi selecionado pelo menos 1 transferencia
        if (count($arTransferencia) > 0) {
            $stParams = http_build_query($arTransferencia);
            $stJs .= "confirmPopUp( 'Baixar Transferências'
                                   ,'Deseja baixar as transferências selecionadas?'
                                   ,'montaParametrosGET(\'baixarTransferencias\');')";
        } else {
            $stJs .= "alertaAviso('Selecione ao menos uma transferência para a baixa','frm','erro','".Sessao::getId()."');";
        }

        echo $stJs;

    }

    /**
     * Metodo que confirma a baixa das transferencias
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function confirmAnularBaixaTransferencia($arParam)
    {
        //Percorre todo os parametros procurando pelas transferencias
        $arTransferencia = array();
        foreach ($arParam as $stKey=>$stValue) {
            if (strstr($stKey,'transferencia')) {
                $arTransferencia[] = explode($stKey,'_');
            }
        }

        //Verifica se foi selecionado pelo menos 1 transferencia
        if (count($arTransferencia) > 0) {
            $stParams = http_build_query($arTransferencia);
            $stJs .= "confirmPopUp( 'Anular Baixa de Transferências'
                                   ,'Deseja anular a baixa das transferências selecionadas?'
                                   ,'montaParametrosGET(\'anularBaixaTransferencias\');')";
        } else {
            $stJs .= "alertaAviso('Selecione ao menos uma baixa de transferência a ser anulada','frm','erro','".Sessao::getId()."');";
        }

        echo $stJs;

    }

    /**
     * Metodo que faz a anulacao da baixa das transferencias
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function anularBaixaTransferencias($arParam)
    {
        //Percorre as transferencias
        foreach ($arParam as $stKey=>$stValue) {
            if (strstr($stKey,'transferencia')) {
                $arTransferencia = explode('_',$stKey);
                $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->inCodLote = $arTransferencia[1];
                $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->bRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade = $arTransferencia[2];
                $this->obModel->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio = $arTransferencia[3];
                $this->obModel->obRTesourariaTransferencia->obRTesourariaAutenticacao->stTipo = $arTransferencia[4];

                $this->obModel->listChequesTransferencia($rsCheque);

                while (!$rsCheque->eof()) {
                    $this->obModel->stNumCheque                                                 = $rsCheque->getCampo('num_cheque'        );
                    $this->obModel->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco = $rsCheque->getCampo('cod_banco'         );
                    $this->obModel->obRMONContaCorrente->obRMONAgencia->inCodAgencia            = $rsCheque->getCampo('cod_agencia'       );
                    $this->obModel->obRMONContaCorrente->inCodigoConta                          = $rsCheque->getCampo('cod_conta_corrente');
                    $this->obModel->stTimestampEmissao                                          = $rsCheque->getCampo('timestamp_emissao' );
                    $obErro = $this->obModel->anularBaixaChequeEmissao($boTransacao);

                    $rsCheque->proximo();
                }
            }
        }

        if (!$obErro->ocorreu()) {
            $stJs .= "alertaAviso('Baixa da Transferência anulada com sucesso','" . $arParam['stAcao'] . "','aviso','" . Sessao::getId() . "','../');";
            $stJs .= "mudaTelaPrincipal('LSManterChequeEmissaoBaixa.php?" . Sessao::getId() . "&stAcao=" . $arParam['stAcao'] . "');";
        } else {
            $stJs .= "alertaAviso('" . $obErro->getDescricao() . "','frm','erro','".Sessao::getId()."');";
        }

        echo $stJs;
    }

    /**
     * Metodo que faz a baixa das transferencias
     *
     * @author      Analista      Tonismar Bernardo   <tonismar.bernardo@cnm.org.br>
     * @author      Desenvolvedor Henrique Boaventura <henrique.boaventura@cnm.org.br>
     * @param array $arParam Array de dados
     *
     * @return void
     */
    public function baixarTransferencias($arParam)
    {
        //Percorre as transferencias
        foreach ($arParam as $stKey=>$stValue) {
            if (strstr($stKey,'transferencia')) {
                $arTransferencia = explode('_',$stKey);
                $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->obRContabilidadeLancamento->obRContabilidadeLote->inCodLote = $arTransferencia[1];
                $this->obModel->obRTesourariaTransferencia->obRContabilidadeLancamentoValor->bRContabilidadeLancamento->obRContabilidadeLote->obROrcamentoEntidade->inCodigoEntidade = $arTransferencia[2];
                $this->obModel->obRTesourariaTransferencia->roRTesourariaBoletim->stExercicio = $arTransferencia[3];
                $this->obModel->obRTesourariaTransferencia->obRTesourariaAutenticacao->stTipo = $arTransferencia[4];

                $this->obModel->listChequesTransferencia($rsCheque);

                while (!$rsCheque->eof()) {
                    $this->obModel->stNumCheque                                                 = $rsCheque->getCampo('num_cheque'        );
                    $this->obModel->obRMONContaCorrente->obRMONAgencia->obRMONBanco->inCodBanco = $rsCheque->getCampo('cod_banco'         );
                    $this->obModel->obRMONContaCorrente->obRMONAgencia->inCodAgencia            = $rsCheque->getCampo('cod_agencia'       );
                    $this->obModel->obRMONContaCorrente->inCodigoConta                          = $rsCheque->getCampo('cod_conta_corrente');
                    $this->obModel->stTimestampEmissao                                          = $rsCheque->getCampo('timestamp_emissao' );
                    $obErro = $this->obModel->baixarChequeEmissao($boTransacao);

                    $rsCheque->proximo();
                }
            }
        }

        if (!$obErro->ocorreu()) {
            $stJs .= "alertaAviso('Transferência baixada com sucesso','" . $arParam['stAcao'] . "','aviso','" . Sessao::getId() . "','../');";
            $stJs .= "mudaTelaPrincipal('LSManterChequeEmissaoBaixa.php?" . Sessao::getId() . "&stAcao=" . $arParam['stAcao'] . "');";
        } else {
            $stJs .= "alertaAviso('" . $obErro->getDescricao() . "','frm','erro','".Sessao::getId()."');";
        }

        echo $stJs;
    }

}
