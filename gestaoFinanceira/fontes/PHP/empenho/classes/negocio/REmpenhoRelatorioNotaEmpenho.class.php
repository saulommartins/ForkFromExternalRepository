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
    * Classe de Regra para emissão do relatório
    * Data de Criação   : 07/12/2004

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria

    * @package URBEM
    * @subpackage Regra

    $Revision: 30668 $
    $Name$
    $Author: eduardoschitz $
    $Date: 2008-02-27 09:39:18 -0300 (Qua, 27 Fev 2008) $

    * Casos de uso: uc-02.03.03
                    uc-02.03.17
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE_RELATORIO  );
include_once ( CAM_GF_EMP_NEGOCIO     ."REmpenhoEmpenho.class.php"               );
include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoAtributoEmpenhoValor.class.php"  );
include_once ( CAM_GA_ADM_NEGOCIO     ."RCadastroDinamico.class.php"             );
include_once ( CAM_GA_ADM_NEGOCIO     ."RAdministracaoConfiguracao.class.php"                 );

/**
    * Classe de Regra para emissão do Plano de Contas com Banco/Recurso

    * @author Analista: Jorge B. Ribarr
    * @author Desenvolvedor: Diego Barbosa Victoria
*/
class REmpenhoRelatorioNotaEmpenho extends PersistenteRelatorio
{
/**
    * @var String
    * @access Private
*/
var $stExercicio;
/**
    * @var Integer
    * @access Private
*/
var $inCodEntidade;
/**
    * @var Integer
    * @access Private
*/
var $inCodEmpenho;

/**
    * @var Integer
    * @access Private
*/
var $inCodDespesaFixa;

/**
     * @access public
     * @param string $valor
*/
function setExercicio($valor) { $this->stExercicio = $valor; }
/**
     * @access public
     * @param Integer $valor
*/
function setCodEntidade($valor) { $this->inCodEntidade= $valor; }
/**
     * @access public
     * @param Integer $valor
*/
function setCodEmpenho($valor) { $this->inCodEmpenho = $valor; }

/**
     * @access public
     * @param Integer $valor
*/
function setCodDespesaFixa($valor) { $this->inCodDespesaFixa = $valor; }

/**
     * @access Public
     * @return String
*/
function getExercicio() { return $this->stExercicio; }
/**
     * @access Public
     * @return Integer
*/
function getCodEntidade() { return $this->inCodEntidade; }
/**
     * @access Public
     * @return Integer
*/
function getCodEmpenho() { return $this->inCodEmpenho;  }

/**
     * @access Public
     * @return Integer
*/
function getCodDespesaFixa() { return $this->inCodDespesaFixa;  }

/**
    * Método Construtor
    * @access Private
*/
function REmpenhoRelatorioNotaEmpenho()
{
    parent::PersistenteRelatorio();
    $this->obRCadastroDinamico  = new RCadastroDinamico;
    $this->obREmpenhoEmpenho    = new REmpenhoEmpenho;
    $this->obRConfiguracao      = new RAdministracaoConfiguracao;
    $this->obRCadastroDinamico->setPersistenteValores  ( new TEmpenhoAtributoEmpenhoValor );
//    $this->obRCadastroDinamico->setPersistenteAtributos( new TEmpenhoAtributoEmpenho      );
    $this->obRCadastroDinamico->setCodCadastro(1);
    $this->obRCadastroDinamico->obRModulo->setCodModulo(10);
}

/**
    * Método abstrato
    * @access Public
*/
function geraRecordSet(&$arRecordSet , $stOrdem = "")
{
    include_once ( CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenho.class.php"               );
    $obTEmpenhoEmpenho    = new TEmpenhoEmpenho;

    $inConta = 0;
    $arRecordSet = array();
    $stFiltro    = " AND   e.cod_entidade = " . $this->inCodEntidade;
    $stFiltro   .= " AND   e.cod_empenho  = " . $this->inCodEmpenho;
    $obTEmpenhoEmpenho->setDado( 'exercicio' , $this->getExercicio() );

    if($this->getCodDespesaFixa())
        $obTEmpenhoEmpenho->setDado( 'cod_despesas_fixas' , $this->getCodDespesaFixa() );

    $obTEmpenhoEmpenho->setDado( 'stFiltro'  , $stFiltro );
    $obTEmpenhoEmpenho->setDado('cod_entidade', $this->inCodEntidade);
    $obTEmpenhoEmpenho->setDado('cod_empenho', $this->inCodEmpenho);

    $this->obRConfiguracao->consultarMunicipio();
    $stNomMunicipio = $this->obRConfiguracao->getNomMunicipio();

    $obErro = $obTEmpenhoEmpenho->recuperaRelatorioEmpenho( $rsRecordSet, '', $stOrdem );

    if ( !$obErro->ocorreu() && !$rsRecordSet->eof() ) {
        $this->obREmpenhoEmpenho->setExercicio                           ( $this->stExercicio );
        $this->obREmpenhoEmpenho->setCodEmpenho                          ( $this->inCodEmpenho );
        $this->obREmpenhoEmpenho->obROrcamentoEntidade->setCodigoEntidade( $this->inCodEntidade );
        $this->obREmpenhoEmpenho->consultar();
        $arChaveAtributo =  array( "cod_pre_empenho" => $this->obREmpenhoEmpenho->getCodPreEmpenho(),
                                   "exercicio"       => $this->stExercicio       );
        $this->obRCadastroDinamico->setChavePersistenteValores( $arChaveAtributo );
        $this->obRCadastroDinamico->recuperaAtributosSelecionadosValores( $rsAtributos );
    }

    $stFiltro  = " WHERE exercicio        =  '".$this->getExercicio()."'";
    $stFiltro .= " AND   cod_pre_empenho  =  ".$this->obREmpenhoEmpenho->getCodPreEmpenho();
    $stOrdem   = " ORDER BY num_item";
    $obErro = $obTEmpenhoEmpenho->recuperaRelatorioEmpenhoItens( $rsItens, $stFiltro, $stOrdem );

    if ( !$obErro->ocorreu() && !$rsRecordSet->eof() && !$rsItens->eof() ) {

        //Linha0
        $arLinha0[0]['entidade']       = $rsRecordSet->getCampo('cod_entidade') . ' - ' . $rsRecordSet->getCampo('nom_entidade');
        $arLinha0[0]['cod_entidade']   = $rsRecordSet->getCampo('cod_entidade');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha0);
        $arRecordSet[$inConta++] = $rsNewRecord;

        //Linha1
        $arLinha1[0]['Orgao']   = $rsRecordSet->getCampo('num_nom_orgao');

        $stUnidade = str_replace( chr(10), "", $rsRecordSet->getCampo('num_nom_unidade') );
        $stUnidade = wordwrap( $stUnidade, 45, chr(13) );
        $arUnidade = explode( chr(13), $stUnidade );
        $inCount = 0;
        foreach ($arUnidade as $stUnidade) {
            $arLinha1[$inCount]['Unidade'] = $stUnidade;
            $inCount++;
        }

        $arLinha1[0]['Tipo']    = $rsRecordSet->getCampo('nom_tipo_pre_empenho');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha1);
        $arRecordSet[$inConta++] = $rsNewRecord;

        //Linha2
        $stDescricao = $rsRecordSet->getCampo('dotacao_reduzida')." - ".$rsRecordSet->getCampo('dotacao_formatada')." - ".$rsRecordSet->getCampo( "nom_conta" );
        $stDescricao = str_replace( chr(10), "", $stDescricao );
        $stDescricao = wordwrap( $stDescricao, 98, chr(13) );
        $arDescricao = explode( chr(13), $stDescricao );
        $inCount = 0;
        foreach ($arDescricao as $stDescricao) {
            $arLinha2[$inCount]['Dotacao'] = $stDescricao;
            $inCount++;
        }
        $arLinha2[0]['Recurso'] = $rsRecordSet->getCampo('nom_recurso');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha2);
        $arRecordSet[$inConta++] = $rsNewRecord;

        //Linha3
        $arLinha3[0]['Credor']  = $rsRecordSet->getCampo('numcgm').' - '.$rsRecordSet->getCampo('nom_cgm');
        $arLinha3[0]['CpfCnpj'] = $rsRecordSet->getCampo('cpf_cnpj');
        $arLinha3[0]['Cgm']     = $rsRecordSet->getCampo('numcgm');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha3);
        $arRecordSet[$inConta++] = $rsNewRecord;

        //Linha4
        $arLinha4[0]['Endereco']= $rsRecordSet->getCampo('endereco');
        $arLinha4[0]['Fone']    = $rsRecordSet->getCampo('fone');
        $arLinha4[0]['Cidade']  = $rsRecordSet->getCampo('nom_municipio');
        $arLinha4[0]['Uf']      = $rsRecordSet->getCampo('sigla_uf');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha4);
        $arRecordSet[$inConta++] = $rsNewRecord;

        //Linha5
        if ($rsRecordSet->getCampo('cod_autorizacao')) {
            $arLinha5[0]['Autorizacao'] = $rsRecordSet->getCampo('cod_autorizacao').' / '.$this->stExercicio;
        } else {
            $arLinha5[0]['Autorizacao'] = "Diversos";
        }
        $arLinha5[0]['Emissao']     = $rsRecordSet->getCampo('dt_empenho');
        $arLinha5[0]['Vencimento']  = $rsRecordSet->getCampo('dt_vencimento');
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha5);
        $arRecordSet[$inConta++] = $rsNewRecord;

        //Armazena os valores
        $nuValorOrcado   = $arLinha6[0]['ValorOrcado']   = $rsRecordSet->getCampo('valor_orcado');
        $nuSaldoAnterior = $arLinha6[0]['SaldoAnterior'] = $rsRecordSet->getCampo('saldo_anterior');
        $nuSaldoAnterior = str_replace( '.',',',$nuSaldoAnterior );
        $nuSaldoAnterior = str_replace( ',','.',$nuSaldoAnterior );

        $stRecurso       = $rsRecordSet->getCampo('cod_recurso').' - '.$rsRecordSet->getCampo('nom_recurso');
        $stPAO           = $rsRecordSet->getCampo('num_acao').' - '.$rsRecordSet->getCampo('nom_pao');
    }
    $inCount=0;

    while ( !$rsItens->eof() ) {
        $arLinha7[$inCount]['Item']         = $rsItens->getCampo('num_item');
        $arLinha7[$inCount]['Quantidade']   = $rsItens->getCampo('quantidade');
        $arLinha7[$inCount]['simbolo']      = $rsItens->getCampo('simbolo');
        $arLinha7[$inCount]['ValorUnitario']= number_format( $rsItens->getCampo('valor_unitario'), 4,',','.' );
        $arLinha7[$inCount]['ValorTotal']   = $rsItens->getCampo('valor_total');
        $nuValorEmpenho                    += $rsItens->getCampo('valor_total');
        $cod_item = ($rsItens->getCampo('cod_item')!='') ? $rsItens->getCampo('cod_item')." - " : "";
        $stNomItem = trim(stripslashes(strtoupper($rsItens->getCampo('nom_item')." ".$rsItens->getCampo('complemento'))));
        $stNomItem = str_replace( chr(10), "", $stNomItem );
        $stNomItem = str_replace( chr(13), " ", $stNomItem );
        $stNomItem = wordwrap( $stNomItem, 53, chr(13) );
        $arNomItem = explode( chr(13), $cod_item.$stNomItem );
        foreach ($arNomItem as $stNomItem) {
            $arLinha7[$inCount]['Especificacao'] = SistemaLegado::strtoupper_ptBR($stNomItem);
            $inCount++;
        }

        $rsItens->proximo();
    }
    $rsRecordSet->setPrimeiroElemento();
    $rsItens->setPrimeiroElemento();

    //Linha6
    $arLinha6[0]['ValorOrcado']  = $nuValorOrcado;
    $arLinha6[0]['SaldoAnterior']= $nuSaldoAnterior;
    $arLinha6[0]['ValorEmpenho'] = $nuValorEmpenho;
    $arLinha6[0]['SaldoAtual']   = $nuSaldoAnterior - $nuValorEmpenho;
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha6);
    $rsNewRecord->addFormatacao('ValorOrcado'   ,'NUMERIC_BR');
    $rsNewRecord->addFormatacao('SaldoAnterior' ,'NUMERIC_BR');
    $rsNewRecord->addFormatacao('ValorEmpenho'  ,'NUMERIC_BR');
    $rsNewRecord->addFormatacao('SaldoAtual'    ,'NUMERIC_BR');
    $arRecordSet[$inConta++] = $rsNewRecord;

    $arHistorico[0]['Historico'] = $rsRecordSet->getCampo( "historico" );
    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche( $arHistorico );
    $arRecordSet[$inConta++] = $rsNewRecord;

    //Descricao
    $arDescricao = array();
    $stDescricao = stripslashes(str_replace( chr(10) , "", $rsRecordSet->getCampo('descricao') ));
    $stDescricao = wordwrap( $stDescricao, 105, chr(13) );
    $arMotivo = explode( chr(13), $stDescricao );
    foreach ($arMotivo as $stDescricao) {
        $arDesc[1] = $stDescricao;
        $arDescricao[] = $arDesc;
    }

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arDescricao);
    $arRecordSet[$inConta++] = $rsNewRecord;

    if (count($arLinha7)) {
        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha7);
        //$rsNewRecord->addFormatacao('Quantidade'    ,'NUMERIC_BR_NULL');
        $rsNewRecord->addFormatacao('Quantidade'    ,'NUMERIC_BR_4'); //alterada coluna quantidade da tabela para 4 casas decimais
        $rsNewRecord->addFormatacao('ValorTotal'    ,'NUMERIC_BR');
        $arRecordSet[$inConta++] = $rsNewRecord;
        $arLinha8[0]['Recurso'] = $stRecurso;
        $stPAO = "RECURSO: ".$stRecurso." -  PAO: ".$stPAO;
        $inCount = 0;
        $stPAO = wordwrap( $stPAO, 110, chr(13) );
        $arPAO = explode( chr(13), $stPAO );

        foreach ($arPAO as $stPAO) {
            $arLinha8[$inCount]['PAO']= $stPAO;
            $arLinha8[0]['Total']   = number_format($nuValorEmpenho,2,',','.');
            $inCount++;
        }

        $rsNewRecord = new RecordSet;
        $rsNewRecord->preenche($arLinha8);
        $arRecordSet[$inConta++] = $rsNewRecord;
    }
    $inCount    = 0;
    $arLinha9   = array();

    while ( !$rsAtributos->eof() ) {
        $arLinha9[$inCount]['Nome']     = $rsAtributos->getCampo('nom_atributo');
        if ($rsAtributos->getCampo('cod_tipo')==3) {
            $arValores      = explode( ",", $rsAtributos->getCampo('valor_padrao') );

            if (in_array( $rsAtributos->getCampo('valor'), $arValores ) ) {
                $inPosicaoValor = array_search( $rsAtributos->getCampo('valor'), $arValores );
                $arDescricoes   = explode( "[][][]", $rsAtributos->getCampo('valor_padrao_desc') );
                $arLinha9[$inCount]['Valor']    = $arDescricoes[ $inPosicaoValor ];
            } else {
                $arLinha9[$inCount]['Valor']    = null;
            }
        } elseif ($rsAtributos->getCampo('cod_tipo')==4) {
            $arDescricoes   = explode("[][][]",$rsAtributos->getCampo('valor_padrao_desc'));
            $arValores      = explode(",",$rsAtributos->getCampo('valor_desc'));
            $stValor        = "";
            for ($inIndice=0; $inIndice<count($arDescricoes); $inIndice++) {
                $stValor = $arDescricoes[ $arValores[($inIndice-1)] ];
            }
            $arLinha9[$inCount]['Valor']    = $stValor;
        } else {
            $arLinha9[$inCount]['Valor']    = ($rsAtributos->getCampo('valor')?$rsAtributos->getCampo('valor'):$rsAtributos->getCampo('valor_padrao'));
        }
        $inCount++;
        $rsAtributos->proximo();
    }

    // Categoria do Empenho
    $arLinha9[$inCount]['Nome'] = 'Categoria';
    $arLinha9[$inCount]['Valor'] = $rsRecordSet->getCampo('categoria');
    $inCount++;

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arLinha9);
    $arRecordSet[$inConta++] = $rsNewRecord;

    // Faz a pesquisa das assinaturas marcadas para aquele empenho
    include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoEmpenhoAssinatura.class.php";
    $obTEmpenhoEmpenhoAssinatura = new TEmpenhoEmpenhoAssinatura;
    $obTEmpenhoEmpenhoAssinatura->setDado('exercicio', $this->getExercicio());
    $obTEmpenhoEmpenhoAssinatura->setDado('cod_entidade', $this->getCodEntidade());
    $obTEmpenhoEmpenhoAssinatura->setDado('cod_empenho', $this->getCodEmpenho());
    $obTEmpenhoEmpenhoAssinatura->recuperaAssinaturasEmpenho($rsAssinaturas);
    $arPapel = $obTEmpenhoEmpenhoAssinatura->arrayPapel();

    $arDescricaoPapel = array();
    // Monta o array com a descrição e cargo de quem está marcado nas assinaturas
    // Para assim poder usa-los na hora de montar as assinaturas do relatório
    while (!$rsAssinaturas->eof()) {
        foreach ($arPapel as $stChave => $inCodPapel) {
            if ($rsAssinaturas->getCampo('num_assinatura') == $inCodPapel) {
                $arDescricaoPapel[$stChave]['nome'] = $rsAssinaturas->getCampo('nom_cgm');
                $arDescricaoPapel[$stChave]['cargo'] = $rsAssinaturas->getCampo('cargo');
                break;
            }
        }
        $rsAssinaturas->proximo();
    }

    $arAssinatura = array();
    $arMes   = array ('01'=>"Janeiro", '02'=>"Fevereiro", '03'=>"Março",  '04'=>"Abril",    '05'=>"Maio",     '06'=>"Junho",
                      '07'=>"Julho",   '08'=>"Agosto",    '09'=>"Setembro",     '10'=>"Outubro", '11'=>"Novembro",'12'=>"Dezembro");
    list($dia,$mes,$ano) = explode('/',$rsRecordSet->getCampo('dt_empenho'));
    $arAssinatura[0][ 'autorizo'   ] = $stNomMunicipio.", ".$dia." de ".$arMes[$mes]." de ".$ano.".";
    $arAssinatura[0][ 'contadoria' ] = 'EMPENHADO';
    $arAssinatura[1][ 'autorizo'   ] = '';
    $arAssinatura[1][ 'contadoria' ] = '';
    $arAssinatura[1][ 'pague'      ] = '';
    $arAssinatura[2][ 'autorizo'   ] = '';
    $arAssinatura[2][ 'contadoria' ] = '______________________________';
    $arAssinatura[2][ 'pague'      ] = '';
    $arAssinatura[3][ 'autorizo'   ] = '';
    $arAssinatura[3][ 'contadoria' ] = ($arDescricaoPapel['conferido']['nome'] != "" ? $arDescricaoPapel['conferido']['nome'] : 'Conferido');
    $arAssinatura[3][ 'pague'      ] = '';
    $arAssinatura[4][ 'autorizo'   ] = '______________________________';
    $arAssinatura[4][ 'contadoria' ] = ($arDescricaoPapel['conferido']['cargo'] != "" ? $arDescricaoPapel['conferido']['cargo'] : '');
    $arAssinatura[4][ 'pague'      ] = '______________________________';
    $arAssinatura[5][ 'autorizo'   ] = ($arDescricaoPapel['ordenador']['nome'] != "" ? $arDescricaoPapel['ordenador']['nome'] : 'Ordenador da Despesa');
    $arAssinatura[5][ 'contadoria' ] = '';
    $arAssinatura[5][ 'pague'      ] = ($arDescricaoPapel['paguese']['nome'] != "" ? $arDescricaoPapel['paguese']['nome'] : '');
    $arAssinatura[6][ 'autorizo'   ] = ($arDescricaoPapel['ordenador']['cargo'] != "" ? $arDescricaoPapel['ordenador']['cargo'] : '');
    $arAssinatura[6][ 'contadoria' ] = '______________________________';
    $arAssinatura[6][ 'pague'      ] = ($arDescricaoPapel['paguese']['cargo'] != "" ? $arDescricaoPapel['paguese']['cargo'] : '');
    $arAssinatura[7][ 'autorizo'   ] = '';
    $arAssinatura[7][ 'contadoria' ] = ($arDescricaoPapel['contador']['nome'] != "" ? $arDescricaoPapel['contador']['nome'] : 'Contador');
    $arAssinatura[7][ 'pague'      ] = '';
    $arAssinatura[8][ 'autorizo'   ] = '';
    $arAssinatura[8][ 'contadoria' ] = ($arDescricaoPapel['contador']['cargo'] != "" ? $arDescricaoPapel['contador']['cargo'] : '');
    $arAssinatura[8][ 'pague'      ] = '';

    $rsNewRecord = new RecordSet;
    $rsNewRecord->preenche($arAssinatura);
    $arRecordSet[$inConta++] = $rsNewRecord;

}

}
