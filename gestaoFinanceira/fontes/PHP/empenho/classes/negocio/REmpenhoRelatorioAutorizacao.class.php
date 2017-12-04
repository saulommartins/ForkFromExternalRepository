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
    * Classe de Regra de Negócio Relatorio de Autorizacao de Empenho
    * Data de Criação   : 07/12/2004

    * @author Desenvolvedor: Eduardo Martins

    * @package URBEM
    * @subpackage Regra

    $Revision: 32043 $
    $Name$
    $Author: lbbarreiro $
    $Date: 2008-01-16 11:22:09 -0200 (Qua, 16 Jan 2008) $

    * Casos de uso uc-02.03.02
                   uc-02.03.19
                   uc-02.03.20
*/
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once CLA_PERSISTENTE_RELATORIO; 
include_once CAM_GA_ADM_NEGOCIO."RAdministracaoConfiguracao.class.php";

/**
    * Classe de Regra de Negócio Itens
    * @author Desenvolvedor: Eduardo Martins
*/
class REmpenhoRelatorioAutorizacao extends PersistenteRelatorio
{
    /**
        * @var Object
        * @access Private
    */
    var $obRConfiguracao;
    /**
        * @var Integer
        * @access Private
    */
    var $inDotacao;
    
    /**
         * @access Public
         * @param Object $valor
    */
    public function setRconfiguracao($valor) { $this->obRConfiguracao = $valor; }
    /**
         * @access Public
         * @param Object $valor
    */
    public function setDotacao($valor) { $this->inDotacao = $valor; }
    
    /**
         * @access Public
         * @param Object $valor
    */
    public function getRConfiguracao() { return $this->obRConfiguracao;  }
    /**
         * @access Public
         * @param Object $valor
    */
    public function getDotacao() { return $this->inDotacao;  }
    
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        $this->setRConfiguracao      ( new RAdministracaoConfiguracao             );
    }

    /**
        * Método abstrato
        * @access Public
    */
    public function geraRecordSet(&$arRecordSet, $stOrder = "cod_pre_empenho, num_item", $tipoRelatorio = 'autorizacao' , $stFiltro)
    {
        include_once (CAM_GF_EMP_MAPEAMENTO."TEmpenhoPreEmpenho.class.php"    );
        $obTEmpenhoPreEmpenho = new TEmpenhoPreEmpenho;
    
        $this->obRConfiguracao->setExercicio( Sessao::getExercicio() );
        $this->obRConfiguracao->consultarMunicipio();
        $stNomMunicipio = $this->obRConfiguracao->getNomMunicipio();
    
        $arLinha1 = array();
        $arLinha2 = array();
        $arLinha3 = array();
        $arLinha4 = array();
        $arItens  = array();
        $arTotal  = array();
        $arAssinatura1 = array();
    
        $arMes   = array ('01'=>"Janeiro", '02'=>"Fevereiro",'03'=>"Março", '04'=>"Abril",   '05'=>"Maio",    '06'=>"Junho",
                          '07'=>"Julho",   '08'=>"Agosto",   '09'=>"Setembro",     '10'=>"Outubro", '11'=>"Novembro",'12'=>"Dezembro");
    
        $inCont       = 0;
        $inContItens  = 0;
        $nuTotalItens = 0;
    
        if ( $this->getDotacao() == "" ) {
            $obTEmpenhoPreEmpenho->setDado( "dotacao", 'f' );
        } else {
            $obTEmpenhoPreEmpenho->setDado( "dotacao", 't' );
        }
    
        $obTEmpenhoPreEmpenho->setDado("filtro", $stFiltro );
        $arFiltro = Sessao::read('filtroRelatorio');
        if ($arFiltro['stExercicio']) {
            $obTEmpenhoPreEmpenho->setDado("exercicio", $arFiltro['stExercicio']);
        } else {
            $obTEmpenhoPreEmpenho->setDado("exercicio", Sessao::getExercicio());
        }
        $obErro = $obTEmpenhoPreEmpenho->recuperaRelatorioAutorizacao( $rsRecordSet, "","", $boTransacao );
    
        if ( !$rsRecordSet->eof() ) {
            $stData = $rsRecordSet->getCampo("dt_autorizacao");
            $arData = explode( "/", $stData );
    
            $arLinha1[0]['fornecedor'] = $rsRecordSet->getCampo('nom_cgm');
            $arLinha1[0]['cpf_cnpj']   = $rsRecordSet->getCampo('cpf_cnpj');
            $arLinha1[0]['numcgm']     = $rsRecordSet->getCampo('num_fornecedor');
    
            $rsRecord = new RecordSet;
            $rsRecord->preenche( $arLinha1 );
            $arRecordSet[$inCont++] = $rsRecord;
    
            $arLinha2[0]['endereco'] = $rsRecordSet->getCampo('endereco');
            $arLinha2[0]['telefone'] = $rsRecordSet->getCampo('telefone');
            $arLinha2[0]['cidade']   = $rsRecordSet->getCampo('nom_municipio');
            $arLinha2[0]['uf']       = $rsRecordSet->getCampo('sigla_uf');
    
            $rsRecord = new RecordSet;
            $rsRecord->preenche( $arLinha2 );
            $arRecordSet[$inCont++] = $rsRecord;
    
            $arEntidade[0]['entidade'] = $rsRecordSet->getCampo('cod_entidade').' - '.$rsRecordSet->getCampo("nom_entidade");
            $rsRecord = new RecordSet;
            $rsRecord->preenche( $arEntidade );
            $arRecordSet[10] = $rsRecord;
    
            $arLinha3[0]['orgao']    = $rsRecordSet->getCampo('num_nom_orgao');
    
            $stDescricao = $rsRecordSet->getCampo('num_nom_unidade');
            $stDescricao = str_replace( chr(10), "", $stDescricao );
            $stDescricao = wordwrap( $stDescricao, 44, chr(13) );
            $arDescricao = explode( chr(13), $stDescricao );
            $inCount = 0;
            foreach ($arDescricao as $stDescricao) {
                $arLinha3[$inCount]['unidade'] = $stDescricao;
                $inCount++;
            }
    
            $rsRecord = new RecordSet;
            $rsRecord->preenche( $arLinha3 );
            $arRecordSet[$inCont++] = $rsRecord;
    
            $stDescricao = $rsRecordSet->getCampo('dotacao_reduzida')." - ".$rsRecordSet->getCampo('dotacao')." - ".$rsRecordSet->getCampo( "nom_conta" );
    
            while (substr($stDescricao, -1, 1) == ' ') {
                $stDescricao = substr($stDescricao, 0, -1);
            }
    
            $stDescricao = str_replace( chr(10), "", $stDescricao );
            $stDescricao = wordwrap( $stDescricao, 113, chr(13) );
            $arDescricao = explode( chr(13), $stDescricao );
            $inCount = 0;
            foreach ($arDescricao as $stDescricao) {
                $arLinha4[$inCount]['dotacao'] = $stDescricao;
                $arLinha4[$inCount]['pao']     = $rsRecordSet->getCampo( 'num_acao' ) . " - " . $rsRecordSet->getCampo( 'nom_pao' );
                $arLinha4[$inCount]['recurso'] = $rsRecordSet->getCampo( 'cod_recurso' ) . " - " . $rsRecordSet->getCampo( 'nom_recurso' );
                $inCount++;
            }
    
            $rsRecord = new RecordSet;
            $rsRecord->preenche( $arLinha4 );
            $arRecordSet[$inCont++] = $rsRecord;
            if ($rsRecordSet->getCampo('dt_validade_final')) {
                $arDataValidade[0]['dt_validade_final']  = $rsRecordSet->getCampo('dt_validade_final');
            } else {
                 $arDataValidade[0]['dt_validade_final']  = "Autorização sem Reserva";
            }
    
            $rsRecord = new RecordSet;
            $rsRecord->preenche( $arDataValidade );
            $arRecordSet[$inCont++] = $rsRecord;
    
            $arDescricao = array();
            /**
             * Henrique Boaventura - Em determinado momento houve a necessidade de se retirar tudo antes da '-', mas
             * apartir do dia 08/12 não houve mais essa necessidade
             */
            #$stDescricao = substr($rsRecordSet->getCampo('descricao'),strpos($rsRecordSet->getCampo('descricao'),'-'),strlen($rsRecordSet->getCampo('descricao')));
            $stDescricao = $rsRecordSet->getCampo('descricao');
            $stDescricao = str_replace( chr(10), '', $stDescricao );
            $stDescricao = wordwrap( $stDescricao, 113, chr(13) );
            $arDescricao = explode( chr(13), $stDescricao );
            $inContAux = 0;
            foreach ($arDescricao as $stDescricao) {
                $arDescr[$inContAux]['descricao'] = $stDescricao;
                $inContAux++;
            }
    
            $rsRecord = new RecordSet;
            $rsRecord->preenche( $arDescr );
            $arRecordSet[$inCont++] = $rsRecord;
    
        }
    
        while ( !$rsRecordSet->eof() ) {
            $arItens[$inContItens]['num_item']    = $rsRecordSet->getCampo('num_item');
            $arItens[$inContItens]['quantidade']  = $rsRecordSet->getCampo('quantidade');
            $arItens[$inContItens]['unidade']     = $rsRecordSet->getCampo('simbolo');
            $stNomItem                            = trim($rsRecordSet->getCampo('nom_item'));
            if ( ( $rsRecordSet->getCampo('complemento') ) and (  trim( $rsRecordSet->getCampo('complemento') ) != trim($stNomItem ) ) ) {
                $stNomItem .= ' '.$rsRecordSet->getCampo('complemento');
            }
    
            $arItens[$inContItens]['vl_unitario'] = number_format($rsRecordSet->getCampo('valor_unitario'),4,",",".");
            $arItens[$inContItens]['vl_total']    = number_format($rsRecordSet->getCampo('valor_total')   ,2,",",".");
            $nuTotalItens = $nuTotalItens + $rsRecordSet->getCampo('valor_total');
    
            $arLista[] = $arItens[$inContItens];
            $stComplemento = str_replace( chr(10) , "", $stNomItem );
            $stComplemento = wordwrap( $stComplemento , 44, chr(13) );
            $arComplemento = explode( chr(13), $stComplemento );
            foreach ($arComplemento as $stComplemento) {
                $arItens[$inContItens]["nom_item"]    = $stComplemento;
                $arLista[] = $arItens[$inContItens];
                $inContItens++;
            }
            $arLista[] = array("nom_item"=> "");
    
            $rsRecordSet->proximo();
        }
        $rsRecordSet->setPrimeiroElemento();
        $arTotal[0]['titulo']   = 'Total Geral:';
        $arTotal[0]['total_geral'] = number_format($nuTotalItens, 2, ",",".");
    
        $rsItens = new RecordSet;
        $rsItens->preenche( $arItens );
        $arRecordSet[$inCont++] = $rsItens;
    
        $rsTotal = new RecordSet;
        $rsTotal->preenche( $arTotal );
        $arRecordSet[$inCont++] = $rsTotal;
    
        $arDtAutorizacao[0]['data_autorizacao'] = $stNomMunicipio . ', ____ de __________________ de ________.';
    
        switch ($tipoRelatorio) {
        case 'autorizacao':
    
            // Faz a pesquisa das assinaturas marcadas para aquele empenho
            include_once CAM_GF_EMP_MAPEAMENTO."TEmpenhoAutorizacaoEmpenhoAssinatura.class.php";
            $obTEmpenhoAutorizacaoEmpenhoAssinatura = new TEmpenhoAutorizacaoEmpenhoAssinatura;
            $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('exercicio', $rsRecordSet->getCampo('exercicio'));
            $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('cod_entidade', $rsRecordSet->getCampo('cod_entidade'));
            $obTEmpenhoAutorizacaoEmpenhoAssinatura->setDado('cod_autorizacao', $rsRecordSet->getCampo('cod_autorizacao'));
            $obTEmpenhoAutorizacaoEmpenhoAssinatura->recuperaAssinaturasAutorizacao($rsAssinaturas);
            $arPapel = $obTEmpenhoAutorizacaoEmpenhoAssinatura->arrayPapel();
    
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
    
            $rsData = new RecordSet;
            $rsData->preenche( $arDtAutorizacao );
            $arRecordSet[$inCont++] = $rsData;
    
            $arAssinatura[0]['autorizo'] = 'AUTORIZO';
            $arAssinatura[0]['autorizoempenho']  = 'AUTORIZO O EMPENHO';
    
            $arAssinatura[1]['autorizo'] = '';
            $arAssinatura[1]['autorizoempenho'] = '';
    
            $arAssinatura[2]['autorizo']  = '____________________________________________';
            $arAssinatura[2]['autorizoempenho']  = '____________________________________________';
    
            $arAssinatura[3]['autorizo'] = $arDescricaoPapel['autorizo']['nome'];
            $arAssinatura[3]['autorizoempenho'] = $arDescricaoPapel['autorizoempenho']['nome'];
    
            $arAssinatura[4]['autorizo'] = $arDescricaoPapel['autorizo']['cargo'];
            $arAssinatura[4]['autorizoempenho']  = $arDescricaoPapel['autorizoempenho']['cargo'];
    
            $rsAssinatura = new RecordSet;
            $rsAssinatura->preenche( $arAssinatura );
            $arRecordSet[$inCont++] = $rsAssinatura;
        break;
        case 'anulacao':
            $rsRecordSet->setPrimeiroElemento();
            $arAssinatura[0]['campo'] = 'Autorização anulada em '.$rsRecordSet->getCampo("dt_anulacao");
    
            $arAssinatura[1]['campo'] = 'Motivo:';
            $arAssinatura[2]['campo'] = $rsRecordSet->getCampo("motivo");
    
            $rsAssinatura = new RecordSet;
            $rsAssinatura->preenche( $arAssinatura );
            $arRecordSet[$inCont++] = $rsAssinatura;
        break;
        }
    
        return $obErro;
    }

}
