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
 * Classe Visão de Manter Receita
 *
 * Data de Criação: 24/09/2008
 *
 * Analista: Bruno Ferreira
 * @author Marcio Medeiros <marcio.medeiros@cnm.org.br>
 *
 * $Id $
 *
 * @package URBEM
 * @subpackage ManterReceita
 */

include_once 'VPPAUtils.class.php';
include_once CAM_GF_PPA_COMPONENTES    . 'IPopUpRecurso.class.php';
include_once CAM_GA_NORMAS_COMPONENTES . 'IPopUpNorma.class.php';

class VPPAManterReceita
{

    /**
     * Objeto Negócio
     *
     * @var object
     */
    private $obNegocio;

    /**
     * Objeto VPPAUtils
     *
     * @var object
     */
    private $obUtils;

    /**
     * Método construtor: instancia objeto de controle
     *
     * @param  RPPAManterReceita $obNegocio
     * @return void
     */
    public function __construct(RPPAManterReceita $obNegocio)
    {
        $this->obNegocio = $obNegocio;
        $this->obUtils = new VPPAUtils;
    }

    /**
     * Executa ação recebida na página de processamento (PR).
     */
    public function executarAcao(Request $request)
    {
        Sessao::setTrataExcecao( true );
        $stMetodo = $request->get('stAcao');
        if (is_string($stMetodo)) {
            $this->$stMetodo($request);
        }
        Sessao::encerraExcecao();
    }

    /**
     * Salva uma Receita e seus respectivos recursos.
     *
     * @param  array $arParam
     * @return void
     */
    public function incluir(array $arParams)
    {
        // Informar a camada de negócio que é inclusão
        if ($this->obNegocio->verificarCadastroReceitaPPA($arParams)) {
            $stAviso  = 'Receita (' . $arParams['inCodConta'] . ')';
            $stAviso .= ' já está cadastrada para este PPA!';
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::exibeAviso(urlencode($stAviso), 'n_incluir', 'erro');
        }
        $this->obNegocio->setNovoRegistro(true);
        if ($this->obNegocio->salvarReceita($arParams)) {
            SistemaLegado::alertaAviso('FMManterReceita.php?stAcao=incluir', $arParams['inCodConta'], 'incluir', 'aviso',  Sessao::getID(), '../');
        } else {
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::exibeAviso(urlencode($this->obNegocio->getErro()), 'n_incluir', 'erro');
        }
    }

    /**
     * Altera uma Receita e seus respectivos recursos.
     *
     * @param  array $arParam
     * @return void
     */
    public function alterar(array $arParams)
    {
        $inCodPPA = $arParams['inCodPPA'];

        // Informar a camada de negócio que é alteração
        $this->obNegocio->setNovoRegistro(false);
        if ($this->obNegocio->salvarReceita($arParams)) {
            SistemaLegado::alertaAviso('FLManterReceita.php?stAcao=alterar', $arParams['inCodConta'], 'alterar', 'aviso', Sessao::getID(), '../');
        } else {
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::exibeAviso(urlencode($this->obNegocio->getErro() . '(' . $inCodPPA . ')'), null, 'erro');
        }
    }

    /**
     * Exclui uma Receita e seus respectivos recursos.
     *
     * @param  array $arParam
     * @return void
     */
    public function excluir(array $arParams)
    {
        $inCodPPA   = !empty($arParams['cod_ppa']) ? $arParams['cod_ppa'] : $arParams['inCodPPA'];
        $inCodConta = !empty($arParams['inCodConta']) ? $arParams['inCodConta'] : $arParams['cod_conta'];

        $boResultado = $this->obNegocio->excluirReceita($arParams);

        if ($boResultado) {
            SistemaLegado::alertaAviso('LSManterReceita.php?stAcao=excluir&inCodPPA='.$inCodPPA, $inCodConta, 'excluir', 'aviso', Sessao::getID(), '../');
        } else {
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::exibeAviso(urlencode($this->obNegocio->getErro() . '(' . $inCodPPA . ')'), null, 'erro');
        }
    }

    /**
     * Exclui um recurso e seus respectivos valores
     *
     * @param array $arParams
     */
    public function excluirRecursoReceita(array $arParams)
    {
        if (!$this->obNegocio->excluirRecursoReceita($arParams)) {
            SistemaLegado::LiberaFrames(true,false);
            SistemaLegado::exibeAviso(urlencode($this->obNegocio->getErro()), 'n_' . __FUNCTION__, 'erro');
        }
    }

    /**
     * Verifica se um PPA está homologado
     * Retorna true se o PPA estiver homologado
     *
     * @param  int  $inCodPPA
     * @return bool
     */
    public function isPPAHomologado($inCodPPA)
    {
        return $this->obNegocio->isPPAHomologado($inCodPPA);
    }

    /**
     * Recupera lista de Receitas
     *
     * @param  int    $inCodPPA
     * @param  int    $inCodConta
     * @return object RecordSet
     *
     * @ignore Atualizado para o ticket #14131
     */
    public function recuperaListaReceitas($inCodPPA, $inCodConta = 0)
    {
        return $this->obNegocio->recuperaListaReceitas($inCodPPA, $inCodConta);
    }

    /**
     * Busca o valor total de despesas do PPA.
     *
     * @param  array  $arParams    argumentos recebidos da página
     * @param  bool   $boRetornoJS se a função deve retornar código javascript ou não
     * @return string o valor total de despesa do PPA
     */
    public function recuperaValorTotalPPA(array $arParams, $boRetornoJS = true)
    {
        $stValorTotalPPA = '0,00';

        if ($this->isPPACadastrado($arParams['inCodPPA'])) {
            $flValorTotalPPA = $this->obNegocio->recuperaValorTotalPPA($arParams);
            $stValorTotalPPA = $this->obUtils->floatToStr($flValorTotalPPA);
        }

        if ($boRetornoJS) {
            return "$('lblTotalDespesasPPA').innerHTML = '$stValorTotalPPA';";
        } else {
            return $stValorTotalPPA;
        }
    }

    /**
     * Busca o valor total de todas as Receitas de um PPA
     * Método utilizado na tela Incluir Receita
     *
     * @return string Javascript
     */
    public function recuperaValorTotalReceita(array $arParams, $retornoJs = true)
    {
        if (!empty($arParams['inCodPPA'])) {
            $inCodPPA = (int) $arParams['inCodPPA'];
        } elseif (!empty($arParams['inCodPPATxt'])) {
            $inCodPPA = (int) $arParams['inCodPPATxt'];
        } else {
            $inCodPPA = 0;
        }
        if ($this->isPPACadastrado($inCodPPA) > 0) {
            $stExercicio = $arParams['stExercicio'];
            if (isset($arParams['inCodReceita'])) {
                $inCodReceita = (int) $arParams['inCodReceita'];
            } else {
                $inCodReceita = 0;
            }
            if ($inCodPPA == 0) {
                SistemaLegado::exibeAviso(urlencode('Código do PPA igual a zero()'), '', 'erro');

                return;
            }
            $flValorTotalReceitasPPA = $this->obNegocio->recuperaValorTotalReceita($inCodPPA, $stExercicio, $inCodReceita);
            $flValorTotalReceitasPPA = $this->obUtils->floatToStr($flValorTotalReceitasPPA);
            if ($retornoJs) {
                $stJs = " document.getElementById('lblTotalReceitasPPA').innerHTML = '$flValorTotalReceitasPPA';     \n";

                return $stJs;
            } else {
                return $flValorTotalReceitasPPA;
            }
        }
    }

    /**
     * Exibe o PopUpNorma se o PPA estiver homologado
     *
     * @return string Javascript
     */
    public function montaSpanNorma()
    {
        if (!empty($_REQUEST['inCodPPA'])) {
            $inCodPPA = (int) $_REQUEST['inCodPPA'];
        } elseif (!empty($_REQUEST['inCodPPATxt'])) {
            $inCodPPA = (int) $_REQUEST['inCodPPATxt'];
        } else {
            $inCodPPA = 0;
        }

        if ($inCodPPA > 0) {
            $stJs = '';
            if ($this->isPPACadastrado($inCodPPA) > 0) {
                if ($this->obNegocio->isPPAHomologado($inCodPPA)) {
                    $obFormulario  = new Formulario;
                    $obIPopUpNorma = new IPopUpNorma();
                    $obIPopUpNorma->obInnerNorma->obCampoCod->stId = 'inCodNorma';
                    $obIPopUpNorma->obInnerNorma->setRotulo("Número da Norma");
                    $obIPopUpNorma->obLblDataNorma->setRotulo( "Data da Norma" );
                    $obIPopUpNorma->obLblDataPublicacao->setRotulo( "Data da Publicação da Norma" );
                    $obIPopUpNorma->setExibeDataNorma(true);
                    $obIPopUpNorma->setExibeDataPublicacao(true);
                    $obIPopUpNorma->geraFormulario( $obFormulario );
                    $obFormulario->montaInnerHTML();
                    $stJs = "$('spnNorma').innerHTML = '". $obFormulario->getHTML(). "';\n";
                 } else {
                    $stJs = "$('spnNorma').innerHTML = '';\n";
                }
            }

            return $stJs;
        }
    }

    /**
     * Verifica se uma Receita já está cadastrada para o PPA.
     *
     * @param  array $arParams
     * @return void
     */
    public function verificarCadastroReceitaPPA(array $arParams)
    {
        if ($arParams['inCodConta'] != '') {
            if ($this->obNegocio->verificarCadastroReceitaPPA($arParams)) {
                $stAviso  = 'Receita ('.$arParams['inCodConta']. ')';
                $stAviso .= ' já está cadastrada para este PPA!';
                $stJs  = " alertaAviso('$stAviso', 'form', 'erro', '". Sessao::getId() ."');    \n";
                $stJs .= " document.getElementById('inCodConta').value = '';             \n";
                $stJs .= " document.getElementById('stDescricaoReceita').innerHTML = '&nbsp;';   \n";

                return $stJs;
            }
        }
    }

    /**
     * Verifica se uma Norma já está cadastrada para a Receita.
     *
     * @param array $arParams
     * @return
     *
     * @deprecated desde 06/01/2009
     */
    public function verificarCadastroReceitaNorma(array $arParams)
    {
        if ($arParams['inCodNorma'] != '') {
            if ($this->obNegocio->verificarCadastroReceitaNorma($arParams) > 0) {
                $stAviso  = 'Norma código '.$arParams['inCodNorma'];
                $stAviso .= ' já cadastrado para esta Receita!';
                $stJs  = " alertaAviso('$stAviso', 'form', 'erro', '". Sessao::getId() ."');    \n";
                $stJs .= " document.getElementById('inCodNorma').value = '';                    \n";
                $stJs .= " document.getElementById('stNorma').innerHTML = '&nbsp;';             \n";
                $stJs .= " document.getElementById('stDataNorma').innerHTML = '&nbsp;';         \n";
                $stJs .= " document.getElementById('stDataPublicacao').innerHTML = '&nbsp;';    \n";

                return $stJs;
            }
        }
    }

    /**
     * Método para montar os Spans 1 e 2 da tela Incluir Receitas
     *
     * @param array
     */
    public function montaValorRecurso(array $arParams)
    {
        $obFormulario = new Formulario;
        // Valores comum a todos os campos:
        $inSize      = 12;
        $inMaxLength = 14;
        $flMaxValue  = 9999999999.99;

        if ($arParams['boValor'] == 'total') {
            $obTxtValorTotal = new Numerico;
            $obTxtValorTotal->setRotulo   ("Total para 4 anos");
            $obTxtValorTotal->setTitle    ("Total para 4 anos");
            $obTxtValorTotal->setName     ("flValorTotal");
            $obTxtValorTotal->setId       ("flValorTotal");
            $obTxtValorTotal->setDecimais (2);
            $obTxtValorTotal->setMaxValue ($flMaxValue);
            $obTxtValorTotal->setNull     (false);
            $obTxtValorTotal->setNegativo (false);
            $obTxtValorTotal->setNaoZero  (true);
            $obTxtValorTotal->setSize     ($inSize);
            $obTxtValorTotal->setMaxLength($inMaxLength);
            $obFormulario->addComponente  ($obTxtValorTotal);
        } elseif ($arParams['boValor'] == 'ano') {
            $obFormulario->addTitulo('Informe valor para pelo menos um ano');
            // Valor Anos 1 0 4
            for ($i = 1; $i <= 4; $i++) {
                $obTxtValorAno = new Numerico;
                $obTxtValorAno->setRotulo   ("Valor Ano $i");
                $obTxtValorAno->setTitle    ("Valor Ano $i");
                $obTxtValorAno->setName     ("flValorAno$i");
                $obTxtValorAno->setId       ("flValorAno$i");
                $obTxtValorAno->setDecimais (2);
                $obTxtValorAno->setMaxValue ($flMaxValue);
                $obTxtValorAno->setNegativo (false);
                $obTxtValorAno->setNaoZero  (true);
                $obTxtValorAno->setSize     ($inSize);
                $obTxtValorAno->setMaxLength($inMaxLength);
                $obFormulario->addComponente($obTxtValorAno);
            }
        }
        $obFormulario->montaInnerHTML();
        $stFormValorReceita = $obFormulario->getHTML();
        $stJs = " d.getElementById('spnValorReceita').innerHTML	= '{$stFormValorReceita}';\n";

        return $stJs;
    }

    /**
     * Monta a Lista de Recursos da receita.
     * Adiciona na lista o recurso postado pelo formulário e
     * varre lista para armazenar juntamente com os dados postados.
     * Método chamado após o a ação de incluir Recurso na tela "Incluir Recurso".
     *
     * @param  array  $arParams
     * @return string Javascript contendo a lista de Recursos
     */
    public function montaListaIncluiRecurso(array $arParams)
    {
        $stHTML = ''; // Html onde será armazenada a lista atual
        // Define cabeçalhos da lista.
        $arCabecalhos = array(
            array('cabecalho' => 'Recurso', 'width' => 30),
            array('cabecalho' => 'Ano 1',   'width' => 8),
            array('cabecalho' => 'Ano 2',   'width' => 8),
            array('cabecalho' => 'Ano 3',   'width' => 8),
            array('cabecalho' => 'Ano 4',   'width' => 8),
            array('cabecalho' => 'Total',   'width' => 8)
        );
        $arValoresLinha  = array();
        $flValorPrevisto = 0; // Valor total da Receita
        $arValorAno      = array();
        $inLinha         = 0; // índice da linha da Lista de Recursos
        if (isset($arParams['inSizeRecurso'])) {
            $numRecursosLista = (int) $arParams['inSizeRecurso'];
        } else {
            $numRecursosLista = 0;
        }
        // Verifica se existe lista montada e armazenar com o valores do formulário
        if ($numRecursosLista > 0) {
            // Reordenar as chaves do array (são desordenadas quando há exclusão de item da lista)
            $this->reordenaValoresLista($arParams);
            for ($inLinha = 0; $inLinha < $numRecursosLista; $inLinha++) { // Varre linhas
                $nomeRecurso = $arParams['hdnNomRecurso'][$inLinha];
                if ($arParams['arTipoValor'][$inLinha] == 'ano') { // Valor por Ano
                    $tipoValorRecurso = 'ano';

                    for ($inAno = 1; $inAno <= 4; $inAno++) {

                        if ($arParams["arValorAno{$inAno}"][$inLinha] > 0) {
                            // Tem valor para este ano
                            $arValorAno[$inAno] = $arParams["arValorAno{$inAno}"][$inLinha];
                        } else {
                            $arValorAno[$inAno] = '0,00';
                        }
                    } // end for
                   $flValorTotal = '&nbsp;'; // Valor total não deve aparecer na linha de valor por ano
                } else {
                    // Valor Total
                    $tipoValorRecurso = 'total';
                    $arValorAno[1]    = '&nbsp;';
                    $arValorAno[2]    = '&nbsp;';
                    $arValorAno[3]    = '&nbsp;';
                    $arValorAno[4]    = '&nbsp;';
                    $flValorTotal     = $arParams['arValorTotal'][$inLinha];
                }
                $stOnBlurValorAno    = "recalcularValorReceita(this, event, 'ano');";
                $stOnBlurValorTotal  = "recalcularValorReceita(this, event, 'total');";
                $tipoCampoValorAno   = 'Numerico';
                $tipoCampoValorTotal = 'Numerico';
                if ($tipoValorRecurso == 'total') {
                    $tipoCampoValorAno = 'Label'; // Ano não pode ser editado
                    $stOnBlurValorAno  = null; // Não há evento
                } else {
                    $tipoCampoValorTotal = 'Label'; // Valor Total não pode ser editado
                    $stOnBlurValorTotal  = null;  // Não há evento
                }
                $arValoresColuna = array( 'tipoValorRecurso'    => $tipoValorRecurso,
                                          'inCodRecurso'        => $arParams['arCodRecurso'][$inLinha],
                                          'nomeRecurso'         => $nomeRecurso,
                                          'hdnNomRecurso'       => $nomeRecurso,
                                          'tipoCampoValorAno'   => $tipoCampoValorAno,
                                          'tipoCampoValorTotal' => $tipoCampoValorTotal,
                                          'arValorAno'          => $arValorAno,
                                          'flValorTotal'        => $flValorTotal,
                                          'stOnBlurValorAno'    => $stOnBlurValorAno,
                                          'stOnBlurValorTotal'  => $stOnBlurValorTotal,
                                          'maxlengthValorAno'   => 12,
                                          'maxlengthValorTotal' => 14 );

                $this->montaArrayValoresLinha($arValoresLinha, $inLinha, $arValoresColuna);
            } // end for
        } // end if $numRecursosLista

        $tipoValorRecurso = strtolower($arParams['boValor']);
        $inCodRecurso     = (int) $arParams['inCodRecurso'];
        $nomeRecurso      = $arParams['hdnDescPopUpRecurso'];
        $arValorAno       = array();

        if ($tipoValorRecurso == 'ano') {
            // Valor por Ano
            if (empty($arParams['flValorAno1'])) {
                $arParams['flValorAno1'] = '0,00';
            }
            if (empty($arParams['flValorAno2'])) {
                $arParams['flValorAno2'] = '0,00';
            }
            if (empty($arParams['flValorAno3'])) {
                $arParams['flValorAno3'] = '0,00';
            }
            if (empty($arParams['flValorAno4'])) {
                $arParams['flValorAno4'] = '0,00';
            }
            $arValorAno[1] = $arParams['flValorAno1'];
            $arValorAno[2] = $arParams['flValorAno2'];
            $arValorAno[3] = $arParams['flValorAno3'];
            $arValorAno[4] = $arParams['flValorAno4'];
            $flValorTotal  = '&nbsp;';
        } else {
            // Valor Total
            $arValorAno[1] = '&nbsp;';
            $arValorAno[2] = '&nbsp;';
            $arValorAno[3] = '&nbsp;';
            $arValorAno[4] = '&nbsp;';
            $flValorTotal = $arParams['flValorTotal'];
        }

        $stOnBlurValorAno    = "recalcularValorReceita(this, event, 'ano' );";
        $stOnBlurValorTotal  = "recalcularValorReceita(this, event, 'total');";
        $tipoCampoValorAno   = 'Numerico';
        $tipoCampoValorTotal = 'Numerico';
        if ($tipoValorRecurso == 'total') {
            $tipoCampoValorAno = 'Label'; // Ano não pode ser editado
            $stOnBlurValorAno = null; // Não há evento
        } else {
            $tipoCampoValorTotal = 'Label'; // Valor Total não pode ser editado
            $stOnBlurValorTotal = null;  // Não há evento
        }
        $arValoresColuna = array( 'tipoValorRecurso'    => $tipoValorRecurso,
                                  'inCodRecurso'        => $inCodRecurso,
                                  'nomeRecurso'         => $nomeRecurso,
                                  'hdnNomRecurso'       => $nomeRecurso,
                                  'tipoCampoValorAno'   => $tipoCampoValorAno,
                                  'tipoCampoValorTotal' => $tipoCampoValorTotal,
                                  'arValorAno'          => $arValorAno,
                                  'flValorTotal'        => $flValorTotal,
                                  'stOnBlurValorAno'    => $stOnBlurValorAno,
                                  'stOnBlurValorTotal'  => $stOnBlurValorTotal,
                                  'maxlengthValorAno'   => 12,
                                  'maxlengthValorTotal' => 14 );
        // Adicionar os valores postados pelo formulário
        $this->montaArrayValoresLinha($arValoresLinha, $inLinha, $arValoresColuna);
        $stHTML .= $this->obUtils->montaListaMixed('Recurso', 'Fontes de Recurso', $arCabecalhos, $arValoresLinha);
        $stJs = "$('spnFonteRecurso').innerHTML = '{$stHTML}';      \n";
        unset($arValoresLinha);

        return $stJs;
    } // end function montaListaIncluiRecurso

    /**
     * Atualiza a Lista de Recursos da receita.
     * Varre a lista para armazenar juntamente com os dados postados.
     *
     * @param  array  $arParams
     * @return string Javascript contendo a lista de Recursos
     */
    public function atualizarListaFonteRecursos(array $arParams)
    {
        $stHTML = '';
        // Define cabeçalhos da lista.
        $arCabecalhos = array(
            array('cabecalho' => 'Recurso', 'width' => 30),
            array('cabecalho' => 'Ano 1',   'width' => 8),
            array('cabecalho' => 'Ano 2',   'width' => 8),
            array('cabecalho' => 'Ano 3',   'width' => 8),
            array('cabecalho' => 'Ano 4',   'width' => 8),
            array('cabecalho' => 'Total',   'width' => 8)
        );
        $arValoresLinha  = array();
        $flValorPrevisto = 0; // Valor total da Receita
        $arValorAno      = array();
        $inLinha         = 0; // índice da linha da Lista de Recursos
        if (isset($arParams['inSizeRecurso'])) {
            $numRecursosLista = (int) $arParams['inSizeRecurso'];
        } else {
            $numRecursosLista = 0;
        }
        // Verifica se existe lista montada e armazenar com o valores do formulário
        if ($numRecursosLista > 0) {
            // Reordenar as chaves do array (são desordenadas quando há exclusão de item da lista)
            $this->reordenaValoresLista($arParams);
            for ($inLinha = 0; $inLinha < $numRecursosLista; $inLinha++) { // Varre linhas
                $nomeRecurso = $arParams['hdnNomRecurso'][$inLinha];
                if ($arParams['arTipoValor'][$inLinha] == 'ano') { // Valor por Ano
                    $tipoValorRecurso = 'ano';

                    for ($inAno = 1; $inAno <= 4; $inAno++) {

                        if ($arParams["arValorAno{$inAno}"][$inLinha] > 0) {
                            // Tem valor para este ano
                            $flValorPrevisto += (float) $arParams["arValorAno{$inAno}"][$inLinha]; // TESTE
                            $arValorAno[$inAno] = $arParams["arValorAno{$inAno}"][$inLinha];
                        } else {
                            $arValorAno[$inAno] = '0,00';
                        }
                    } // end for
                   $flValorTotal = '&nbsp;'; // Valor total não deve aparecer na linha de valor por ano
                } else {
                    // Valor Total
                    $tipoValorRecurso = 'total';
                    $arValorAno[1]    = '&nbsp;';
                    $arValorAno[2]    = '&nbsp;';
                    $arValorAno[3]    = '&nbsp;';
                    $arValorAno[4]    = '&nbsp;';
                    $flValorPrevisto += $flValorTotal; // TESTE
                    $flValorTotal     = $arParams['arValorTotal'][$inLinha];
                }
                $stOnBlurValorAno    = "recalcularValorReceita(this, event, 'ano');";
                $stOnBlurValorTotal  = "recalcularValorReceita(this, event, 'total');";
                $tipoCampoValorAno   = 'Numerico';
                $tipoCampoValorTotal = 'Numerico';
                if ($tipoValorRecurso == 'total') {
                    $tipoCampoValorAno = 'Label'; // Ano não pode ser editado
                    $stOnBlurValorAno  = null; // Não há evento
                } else {
                    $tipoCampoValorTotal = 'Label'; // Valor Total não pode ser editado
                    $stOnBlurValorTotal  = null;  // Não há evento
                }
                $arValoresColuna = array( 'tipoValorRecurso'    => $tipoValorRecurso,
                                          'inCodRecurso'        => $arParams['arCodRecurso'][$inLinha],
                                          'nomeRecurso'         => $nomeRecurso,
                                          'hdnNomRecurso'       => $nomeRecurso,
                                          'tipoCampoValorAno'   => $tipoCampoValorAno,
                                          'tipoCampoValorTotal' => $tipoCampoValorTotal,
                                          'arValorAno'          => $arValorAno,
                                          'flValorTotal'        => $flValorTotal,
                                          'stOnBlurValorAno'    => $stOnBlurValorAno,
                                          'stOnBlurValorTotal'  => $stOnBlurValorTotal,
                                          'maxlengthValorAno'   => 12,
                                          'maxlengthValorTotal' => 14 );

                $this->montaArrayValoresLinha($arValoresLinha, $inLinha, $arValoresColuna);
            } // end for
        } // end if $numRecursosLista

        $stHTML .= $this->obUtils->montaListaMixed('Recurso', 'Fontes de Recurso', $arCabecalhos, $arValoresLinha);
        $stJs  = "$('lblTotalReceita').innerHTML  = retornaFormatoMonetario({$flValorPrevisto});         \n";
        $stJs = "$('spnFonteRecurso').innerHTML = '{$stHTML}';      \n";
        unset($arValoresLinha);

        return $stJs;
    } // end function atualizarListaFonteRecursos

    /**
    * Reordena os itens da lista. O array de itens é
    * desordenado quando há exclusão de um item.
    *
    * @param array $arParams
    * @return void $arParams por referência
    */
    private function reordenaValoresLista(&$arParams)
    {
        // Reordenar as chaves do array (são desordenadas quando há exclusão de item da lista)
        $arParams['arTipoValor']   = array_values($arParams['arTipoValor']);
        $arParams['arCodRecurso']  = array_values($arParams['arCodRecurso']);
        $arParams['hdnNomRecurso'] = array_values($arParams['hdnNomRecurso']);
        $arParams['arValorTotal']  = array_values($arParams['hdnArValorTotal']);
        $arParams['arValorAno1']   = array_values($arParams['hdnArValorAno1']);
        $arParams['arValorAno2']   = array_values($arParams['hdnArValorAno2']);
        $arParams['arValorAno3']   = array_values($arParams['hdnArValorAno3']);
        $arParams['arValorAno4']   = array_values($arParams['hdnArValorAno4']);
    }

    /**
     * Busca no banco os recursos da receita e seus respectivos valores
     * Método utilizado por Consultar Receita
     *
     * @return string Javascript para preencher a lista de Recursos
     */
    public function montaListaRecursos()
    {
        $inCodPPA           = (int) $_REQUEST['inCodPPA'];
        $inCodReceitaDados  = (int) $_REQUEST['inCodReceitaDados'];
        $rsReceitaRecursos  = $this->obNegocio->recuperaListaReceitaRecursos($_REQUEST);
        $numReceitaRecursos = count($rsReceitaRecursos->arElementos);
        if ($numReceitaRecursos > 0) {
            $stHTML = '';
            // Define cabeçalhos da lista.
            $arCabecalhos = array(
                array('cabecalho' => 'Recurso', 'width' => 30),
                array('cabecalho' => 'Ano 1',   'width' => 8),
                array('cabecalho' => 'Ano 2',   'width' => 8),
                array('cabecalho' => 'Ano 3',   'width' => 8),
                array('cabecalho' => 'Ano 4',   'width' => 8),
                array('cabecalho' => 'Total',   'width' => 8)
            );

            $arValoresLinha  = array();
            $flValorTotal    = 0;
            $flValorPrevisto = 0; // Valor atual da Receita no banco de dados
            $arValorAno      = array();
            $arTipoValores   = array();
            for ($inLinha = 0; $inLinha < $numReceitaRecursos; $inLinha++) {
                $inCodRecurso = $rsReceitaRecursos->arElementos[$inLinha]['cod_recurso'];
                $rsRecursoValores = $this->obNegocio->recuperaReceitaRecursoValor($rsReceitaRecursos->arElementos[$inLinha]);
                if ($rsRecursoValores->arElementos[0]['ano'] > 0) {
                    $tipoValorRecurso = 'ano';
                    // Valor por Ano - pode ser de 1 até 4 anos
                    $inElemento = 0;
                    for ($inAno = 1; $inAno <= 4; $inAno++) {
                        if (isset($rsRecursoValores->arElementos[$inElemento])) {
                            if ($rsRecursoValores->arElementos[$inElemento]['ano'] == $inAno) {
                                $flValorPrevisto += (float) $rsRecursoValores->arElementos[$inElemento]['valor'];
                                // Tem valor para este ano: armazanar em formato monetário
                                $stValorAno = $this->obUtils->floatToStr($rsRecursoValores->arElementos[$inElemento]['valor']);
                                $arValorAno[$inAno] = $stValorAno;
                                $inElemento++;
                            } else {
                                $arValorAno[$inAno] = '0,00';
                            }
                        } else {
                            $arValorAno[$inAno] = '&nbsp;';
                        }
                    } // end for
                    $flValorTotal = '&nbsp;'; // Valor total não deve aparecer na linha de valor por ano
                } else {
                    // Valor Total
                    $tipoValorRecurso = 'total';
                    $arValorAno[1] = '&nbsp;';
                    $arValorAno[2] = '&nbsp;';
                    $arValorAno[3] = '&nbsp;';
                    $arValorAno[4] = '&nbsp;';
                    $flValorTotal = (float) $rsRecursoValores->arElementos[0]['valor'];
                    $flValorPrevisto += $flValorTotal;
                }

                $nomeRecurso = $rsReceitaRecursos->arElementos[$inLinha]['nom_recurso'];
                if ($flValorTotal > 0) {
                    $stValorTotal = $this->obUtils->floatToStr($flValorTotal);
                } else {
                    $stValorTotal = '&nbsp;';
                }
                $arValoresColuna = array( 'tipoValorRecurso'    => $tipoValorRecurso,
                                          'inCodRecurso'        => $inCodRecurso,
                                          'nomeRecurso'         => $nomeRecurso,
                                          'hdnNomRecurso'       => $nomeRecurso,
                                          'tipoCampoValorAno'   => 'Label',
                                          'tipoCampoValorTotal' => 'Label',
                                          'arValorAno'          => $arValorAno,
                                          'flValorTotal'        => $stValorTotal,
                                          'maxlengthValorAno'   => 12,
                                          'maxlengthValorTotal' => 14 );

                $this->montaArrayValoresLinha($arValoresLinha, $inLinha, $arValoresColuna);
            } // end for

            if ($_REQUEST['boDestinacaoRecurso'] == 't') {
                $stLabelRecursos = 'Destinação de Recurso';
            } else {
                $stLabelRecursos = 'Recursos';
            }
            $stHTML .= $this->obUtils->montaListaMixed('Recurso', $stLabelRecursos, $arCabecalhos, $arValoresLinha, true);
            $stJs .= "$('lblTotalReceita').innerHTML  = retornaFormatoMonetario({$flValorPrevisto});         \n";
            $stJs .= "$('spnFonteRecurso').innerHTML  = '{$stHTML}';                                         \n";
        } else {
            $stJs  ="$('spnFonteRecurso').innerHTML = '';";
            $stJs .= " alertaAviso('Não há valores cadastrados para o Recurso $inCodRecurso!', 'form', 'erro', '". Sessao::getId() ."');     \n";
            $stJs .= " document.getElementById('inCodRecurso').value = '';                              \n";
            $stJs .= " document.getElementById('stDescricaoRecurso').innerHTML = '';                    \n";
        }

        return $stJs;
    } // end function montaValoresAlteraRecurso

    /**
     * Busca no banco os recursos da receita e seus respectivos valores
     * Método chamado após o carregamento da tela de alteração de Receita
     *
     * @return string Javascript para preencher a lista de Recursos
     */
    public function montaListaAlteraRecurso()
    {
        $inCodPPA           = (int) $_REQUEST['inCodPPA'];
        $inCodReceitaDados  = (int) $_REQUEST['inCodReceitaDados'];
        $rsReceitaRecursos  = $this->obNegocio->recuperaListaReceitaRecursos($_REQUEST);
        $numReceitaRecursos = count($rsReceitaRecursos->arElementos);
        if ($numReceitaRecursos > 0) {
            $stHTML = ''; // Html onde será armazenada a lista
            // Define cabeçalhos da lista.
            $arCabecalhos = array(
                array('cabecalho' => 'Recurso', 'width' => 30),
                array('cabecalho' => 'Ano 1',   'width' => 10),
                array('cabecalho' => 'Ano 2',   'width' => 10),
                array('cabecalho' => 'Ano 3',   'width' => 10),
                array('cabecalho' => 'Ano 4',   'width' => 10),
                array('cabecalho' => 'Total',   'width' => 12)
            );
            $arValoresLinha  = array();
            $flValorTotal    = 0;
            $flValorPrevisto = 0; // Valor atual da Receita no banco de dados    public function excluirRecursoReceita($arParams)
            $arValorAno      = array();
            $arTipoValores   = array();
            for ($inLinha = 0; $inLinha < $numReceitaRecursos; $inLinha++) {
                $inCodRecurso = $rsReceitaRecursos->arElementos[$inLinha]['cod_recurso'];
                $rsRecursoValores = $this->obNegocio->recuperaReceitaRecursoValor($rsReceitaRecursos->arElementos[$inLinha]);

                if ($rsRecursoValores->arElementos[0]['ano'] == '0' && $rsRecursoValores->arElementos[0]['valor'] == 0.00) {
                    $tipoValorRecurso = 'ano';
                    // Valor por Ano
                    $inElemento = 1;
                    for ($inAno = 1; $inAno <= 4; $inAno++) {
                        $flValorPrevisto += $rsRecursoValores->arElementos[$inElemento]['valor'];
                        $stValorAno = $this->obUtils->floatToStr($rsRecursoValores->arElementos[$inElemento]['valor']);
                        $arValorAno[$inAno] = $stValorAno;
                        $inElemento++;

                    } // end for
                    $stValorTotal = '&nbsp;'; // Valor total não deve aparecer na linha de valor por ano
                } else {
                    // Valor por Total
                    $tipoValorRecurso = 'total';
                    $arValorAno[1] = '&nbsp;';
                    $arValorAno[2] = '&nbsp;';
                    $arValorAno[3] = '&nbsp;';
                    $arValorAno[4] = '&nbsp;';
                    $flValorTotal  = $rsRecursoValores->arElementos[0]['valor'];
                    $flValorPrevisto += $flValorTotal;
                }
                $stOnBlurValorAno    = "recalcularValorReceita(this, event, 'ano');";
                $stOnBlurValorTotal  = "recalcularValorReceita(this, event, 'total');";
                $tipoCampoValorAno   = 'Numerico';
                $tipoCampoValorTotal = 'Numerico';
                if ($tipoValorRecurso == 'total') {
                    $tipoCampoValorAno = 'Label'; // Ano não pode ser editado
                    $stOnBlurValorAno = null; // Não há evento
                } else {
                    $tipoCampoValorTotal = 'Label'; // Valor Total não pode ser editado
                    $stOnBlurValorTotal = null;  // Não há evento
                }
                $nomeRecurso = $rsReceitaRecursos->arElementos[$inLinha]['nom_recurso'];
                if ($flValorTotal > 0) {
                    $stValorTotal = $this->obUtils->floatToStr($flValorTotal);
                } else {
                    $stValorTotal = '&nbsp;';
                }
                $arValoresColuna = array( 'tipoValorRecurso'    => $tipoValorRecurso,
                                          'inCodRecurso'        => $inCodRecurso,
                                          'nomeRecurso'         => $nomeRecurso,
                                          'hdnNomRecurso'       => $nomeRecurso,
                                          'tipoCampoValorAno'   => $tipoCampoValorAno,
                                          'tipoCampoValorTotal' => $tipoCampoValorTotal,
                                          'arValorAno'          => $arValorAno,
                                          'flValorTotal'        => $stValorTotal,
                                          'stOnBlurValorAno'    => $stOnBlurValorAno,
                                          'stOnBlurValorTotal'  => $stOnBlurValorTotal,
                                          'maxlengthValorAno'   => 10,
                                          'maxlengthValorTotal' => 14 );

                $this->montaArrayValoresLinha($arValoresLinha, $inLinha, $arValoresColuna);
            } // end for
            $stHTML .= $this->obUtils->montaListaMixed('Recurso', 'Fontes de Recurso', $arCabecalhos, $arValoresLinha);
            $stJs  = "$('lblTotalPrevisto').innerHTML = retornaFormatoMonetario({$flValorPrevisto});         \n";
            $stJs .= "$('lblTotalReceita').innerHTML = retornaFormatoMonetario({$flValorPrevisto});         \n";
            $stJs .= "$('spnFonteRecurso').innerHTML = '{$stHTML}';                                         \n";
        } else {
            $stJs  ="$('spnFonteRecurso').innerHTML = '';";
            $stJs .= " alertaAviso('Não há valores cadastrados para o Recurso $inCodRecurso!', 'form', 'erro', '". Sessao::getId() ."');     \n";
            $stJs .= " document.getElementById('inCodRecurso').value = '';                              \n";
            $stJs .= " document.getElementById('stDescricaoRecurso').innerHTML = '';                    \n";
        }

        return $stJs;
    } // end function montaValoresAlteraRecurso

    /**
     * Monta o array para a lista de recursos da Receita
     *
     * @param array $arValoresLinha  Valores que serão enviados ao método monta Lista
     * @param int   $inLinha         Número da linha na lista
     * @param array $arValoresColuna
     *                               Array $arValoresColuna deverá seguir uma estrutura semelhante a esta:
     *                               array( 'tipoValorRecurso'    => $tipoValorRecurso,
     *                               'inCodRecurso'        => $inCodRecurso,
     *                               'nomeRecurso'         => $nomeRecurso,
     *                               'hdnNomRecurso'       => $nomeRecurso,
     *                               'tipoCampoValorAno'   => $tipoCampoValorAno,
     *                               'tipoCampoValorTotal' => $tipoCampoValorTotal,
     *                               'arValorAno'          => $arValorAno,
     *                               'flValorTotal'        => $flValorTotal );
     *
     * @return void $arValoresLinha por referência
     */
    private function montaArrayValoresLinha(&$arValoresLinha, $inLinha, $arValoresColuna)
    {
        $arValoresLinha[$inLinha][] = array( 'tipo'  => 'hidden',
                                             'name'  => 'hdnArValorTotal',
                                             'campo' => 'hdn_valor_total',
                                             'value' => $arValoresColuna['flValorTotal'] );

        $arValoresLinha[$inLinha][] = array( 'tipo'  => 'hidden',
                                             'name'  => 'hdnArValorAno1',
                                             'campo' => 'hdn_valor_ano_1',
                                             'value' => $arValoresColuna['arValorAno'][1] );

        $arValoresLinha[$inLinha][] = array( 'tipo'  => 'hidden',
                                             'name'  => 'hdnArValorAno2',
                                             'campo' => 'hdn_valor_ano_2',
                                             'value' => $arValoresColuna['arValorAno'][2] );

        $arValoresLinha[$inLinha][] = array( 'tipo'  => 'hidden',
                                             'name'  => 'hdnArValorAno3',
                                             'campo' => 'hdn_valor_ano_3',
                                             'value' => $arValoresColuna['arValorAno'][3] );

        $arValoresLinha[$inLinha][] = array( 'tipo'  => 'hidden',
                                             'name'  => 'hdnArValorAno4',
                                             'campo' => 'hdn_valor_ano_4',
                                             'value' => $arValoresColuna['arValorAno'][4] );

        $arValoresLinha[$inLinha][] = array( 'tipo'  => 'hidden',
                                             'name'  => 'arTipoValor',
                                             'campo' => 'tipo_valor',
                                             'value' =>  $arValoresColuna['tipoValorRecurso'] );

        $arValoresLinha[$inLinha][] = array( 'tipo'  => 'hidden',
                                             'name'  => 'arCodRecurso',
                                             'campo' => 'cod_recurso',
                                             'value' =>  $arValoresColuna['inCodRecurso'] );

        $arValoresLinha[$inLinha][] = array( 'tipo'  => 'hidden',
                                             'name'  => 'hdnNomRecurso',
                                             'campo' => 'nom_recurso',
                                             'value' =>  $arValoresColuna['hdnNomRecurso'] );

        $arValoresLinha[$inLinha][] = array( 'tipo'  => 'Label',
                                             'name'  => 'arNomRecurso',
                                             'campo' => 'nom_recurso',
                                             'value' =>  $arValoresColuna['nomeRecurso'] );

        $arValoresLinha[$inLinha][] = array( 'tipo'      => $arValoresColuna['tipoCampoValorAno'],
                                             'name'      => 'arValorAno1',
                                             'campo'     => 'valor_ano_1',
                                             'value'     => $arValoresColuna['arValorAno'][1],
                                             'onBlur'    => $arValoresColuna['stOnBlurValorAno'],
                                             'maxlength' => $arValoresColuna['maxlengthValorAno'] );

        $arValoresLinha[$inLinha][] = array( 'tipo'      => $arValoresColuna['tipoCampoValorAno'],
                                             'name'      => 'arValorAno2',
                                             'campo'     => 'valor_ano_2',
                                             'value'     => $arValoresColuna['arValorAno'][2],
                                             'onBlur'    => $arValoresColuna['stOnBlurValorAno'],
                                             'maxlength' => $arValoresColuna['maxlengthValorAno'] );

        $arValoresLinha[$inLinha][] = array( 'tipo'      => $arValoresColuna['tipoCampoValorAno'],
                                             'name'      => 'arValorAno3',
                                             'campo'     => 'valor_ano_3',
                                             'value'     => $arValoresColuna['arValorAno'][3],
                                             'onBlur'    => $arValoresColuna['stOnBlurValorAno'],
                                             'maxlength' => $arValoresColuna['maxlengthValorAno'] );

        $arValoresLinha[$inLinha][] = array( 'tipo'      => $arValoresColuna['tipoCampoValorAno'],
                                             'name'      => 'arValorAno4',
                                             'campo'     => 'valor_ano_4',
                                             'value'     => $arValoresColuna['arValorAno'][4],
                                             'onBlur'    => $arValoresColuna['stOnBlurValorAno'],
                                             'maxlength' => $arValoresColuna['maxlengthValorAno'] );

        $arValoresLinha[$inLinha][] = array( 'tipo'      => $arValoresColuna['tipoCampoValorTotal'],
                                             'name'      => 'arValorTotal',
                                             'campo'     => 'valor_total',
                                             'value'     => $arValoresColuna['flValorTotal'],
                                             'onBlur'    => $arValoresColuna['stOnBlurValorTotal'],
                                             'maxlength' => $arValoresColuna['maxlengthValorTotal'] );
    }

    /**
     * Verifica se existe destinação de Recurso do PPA em questão.
     *
     * @param  int  $inCodPPA
     * @return bool
     */
    public function pesquisarDestinacaoRecurso($inCodPPA)
    {
        return $this->obNegocio->pesquisarDestinacaoRecurso($inCodPPA);
    }

    /**
     * Monta componente IPopUpRecurso com os parâmetros corretos.
     *
     * @param  array  $arParametros parametros recebidos do formulário
     * @return string código javascript
     */
    public function montarComponenteRecurso(array $arParametros)
    {
        if (!empty($arParametros['inCodPPA'])) {
            $inCodPPA = (int) $arParametros['inCodPPA'];
        } elseif (!empty($arParametros['inCodPPATxt'])) {
            $inCodPPA = (int) $arParametros['inCodPPATxt'];
        } else {
            $inCodPPA = 0;
        }

        if ($inCodPPA > 0) {
            if ($this->isPPACadastrado($inCodPPA) > 0) {
                $boDestinacao = $this->obNegocio->pesquisarDestinacaoRecurso($inCodPPA);
                $obForm          = new Form();
                $obFormulario    = new Formulario();
                $obIPopUpRecurso = new IPopUpRecurso($obForm);
                if ($boDestinacao) {
                    $stRotuloRecurso = 'Recurso Destinação';
                } else {
                    $stRotuloRecurso = 'Recurso';
                }
                $obIPopUpRecurso->obInnerRecurso->setRotulo($stRotuloRecurso);
                $obIPopUpRecurso->setTitle($stRotuloRecurso);
                $obIPopUpRecurso->setUtilizaDestinacao($boDestinacao);
                $obFormulario->addTitulo("Dados para Cadastro de Fontes de Recurso");
                $obFormulario->addComponente($obIPopUpRecurso);
                // ANO/VALOR
                $obValorTotal = new Radio;
                $obValorTotal->setName   ("boValor");
                $obValorTotal->setId     ("btnTotal");
                $obValorTotal->setRotulo ("Tipo de valor");
                $obValorTotal->setTitle  ("Informe se é valor total ou por ano");
                $obValorTotal->setValue  ("total");
                $obValorTotal->setLabel  ("Valor Total");
                $obValorTotal->setNull   (false);
                $obValorTotal->setChecked(false);
                $obValorTotal->obEvento->setOnChange("montaParametrosGET('montaValorRecurso');");
                // Valor por Ano
                $obValorAno = new Radio;
                $obValorAno->setName ("boValor");
                $obValorAno->setId   ("btnAno");
                $obValorAno->setValue("ano");
                $obValorAno->setLabel("Valor por ano");
                $obValorAno->setNull (false);
                $obValorAno->obEvento->setOnChange("montaParametrosGET('montaValorRecurso');");
                $obFormulario->agrupaComponentes(array($obValorTotal, $obValorAno));
                // Receita total ou por ano (Span 1 ou 2)
                $spnValorReceita = new Span();
                $spnValorReceita->setId("spnValorReceita");
                $obFormulario->addSpan ($spnValorReceita);
                // Btn Incluir Recurso
                $obBtnIncluirRecurso = new Button;
                $obBtnIncluirRecurso->setName ("btnIncluirRecurso");
                $obBtnIncluirRecurso->setValue('Incluir');
                $obBtnIncluirRecurso->obEvento->setOnClick("incluirRecurso();");
                // Btn Limpar Recurso
                $obBtnLimparRecurso = new Button;
                $obBtnLimparRecurso->setName ("btnLimparRecurso");
                $obBtnLimparRecurso->setValue('Limpar');
                $obBtnLimparRecurso->obEvento->setOnClick("limparRecurso();");
                $obFormulario->defineBarra(array($obBtnIncluirRecurso, $obBtnLimparRecurso));
                // Montar formulario
                $obFormulario->montaInnerHTML();
                $stHTML = $obFormulario->getHTML();
                $stJS  = "$('spnRecurso').innerHTML = '" . $stHTML . "';";
                $stJS .= "$('boDestinacaoRecurso').value = '" . $boDestinacao . "';";
            }
        } else {
            $stJS  = "$('spnRecurso').innerHTML = '';";
        }

        return $stJS;
    }

    /**
     * Verifica se o PPA ja esta cadastrado
     *
     * @param  int $inCodPPA
     * @return int numero de registro encontrados
     */
    public function isPPACadastrado($inCodPPA)
    {
        return $this->obNegocio->isPPACadastrado($inCodPPA);
    }

} // end class
?>
