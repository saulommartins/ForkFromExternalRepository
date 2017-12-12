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
    * Classe de mapeamento da tabela licitacao.participante_penalidade
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Id: TLicitacaoPenalidadesCertificacao.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-03.05.28
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.participante_penalidade
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoPenalidadesCertificacao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoPenalidadesCertificacao()
{
    parent::Persistente();
    $this->setTabela("licitacao.penalidades_certificacao");

    $this->setCampoCod('');
    $this->setComplementoChave('cod_penalidade,cod_penalidade,num_certificacao,exercicio,cgm_fornecedor');

    $this->AddCampo('cod_penalidade','integer',false,'',true,false);
    $this->AddCampo('num_certificacao','integer',false,'',true,'TLicitacaoParticipanteCertificacaoPenalidade');
    $this->AddCampo('exercicio','varchar',false,'',true,'TLicitacaoParticipanteCertificacaoPenalidade');
    $this->AddCampo('cgm_fornecedor','integer',false,'',true,'TLicitacaoParticipanteCertificacaoPenalidade');
    $this->AddCampo('ano_exercicio','varchar',false,'',false,'TLicitacaoParticipanteCertificacaoPenalidade');
    $this->AddCampo('cod_processo','integer',false,'',false,true);
    $this->AddCampo('valor','numeric',false,'',false,false);
    $this->AddCampo('dt_publicacao','date',false,'',false,false);
    $this->AddCampo('dt_validade','date',false,'',false,false);
    $this->AddCampo('observacao','text',false,'',false,false);
}

function recuperaListaPenalidade(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaListaPenalidade().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaListaPenalidade()
{
    $stSql .= "  select															\n";
    $stSql .= "  	 lpc.cod_penalidade                                         \n";
    $stSql .= "  	,lpc.num_certificacao                                       \n";
    $stSql .= "  	,lpc.exercicio                                              \n";
    $stSql .= "  	,lpc.cgm_fornecedor                                         \n";
    $stSql .= "  	,lpc.ano_exercicio                                          \n";
    $stSql .= "  	,lpc.cod_processo                                           \n";
    $stSql .= "  	,coalesce(lpc.valor,0.00) as valor                          \n";
    $stSql .= "  	,to_char(lpc.dt_publicacao, 'dd/mm/yyyy') as dt_publicacao  \n";
    $stSql .= "  	,to_char(lpc.dt_validade, 'dd/mm/yyyy') as dt_validade      \n";
    $stSql .= "  	,lpc.observacao                                             \n";
    $stSql .= "  	,lp.descricao                                               \n";
    $stSql .= "  from                                                           \n";
    $stSql .= "  	 licitacao.penalidades_certificacao as lpc                  \n";
    $stSql .= "  	,licitacao.penalidade as lp                                 \n";
    $stSql .= "  where                                                          \n";
    $stSql .= "  	lpc.cod_penalidade = lp.cod_penalidade                      \n";
    $stSql .= "  order by                                                       \n";
    $stSql .= "  	lpc.cod_penalidade                                          \n";

    return $stSql;
}

function recuperaListaPenalidadeFornecedor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaListaPenalidadeFornecedor().$stFiltro.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaListaPenalidadeFornecedor()
{
    $stSql .= "  select															\n";
    $stSql .= "  	 lpc.cod_penalidade                                         \n";
    $stSql .= "  	,lpc.num_certificacao                                       \n";
    $stSql .= "  	,lpc.exercicio                                              \n";
    $stSql .= "  	,lpc.cgm_fornecedor                                         \n";
    $stSql .= "  	,lpc.ano_exercicio                                          \n";
    $stSql .= "  	,lpc.cod_processo                                           \n";
    $stSql .= "  	,coalesce(lpc.valor,0.00) as valor                          \n";
    $stSql .= "  	,to_char(lpc.dt_publicacao, 'dd/mm/yyyy') as dt_publicacao  \n";
    $stSql .= "  	,to_char(lpc.dt_validade, 'dd/mm/yyyy') as dt_validade      \n";
    $stSql .= "  	,lpc.observacao                                             \n";
    $stSql .= "  	,lp.descricao                                               \n";
    $stSql .= "  from                                                           \n";
    $stSql .= "  	 licitacao.penalidades_certificacao as lpc                  \n";
    $stSql .= "  	,licitacao.penalidade as lp                                 \n";
    $stSql .= "  where                                                          \n";
    $stSql .= "  	lpc.cod_penalidade = lp.cod_penalidade                      \n";

    if ( $this->getDado( 'num_certificacao' ) ) {
        $stSql .= " and lpc.num_certificacao = ".$this->getDado( 'num_certificacao' )." \n";
    }

    if ( $this->getDado( 'cgm_fornecedor' ) ) {
        $stSql .= " and lpc.cgm_fornecedor = ".$this->getDado( 'cgm_fornecedor' )." \n";
    }

    $stSql .= "  order by                                                       \n";
    $stSql .= "  	lpc.cod_penalidade                                          \n";

    return $stSql;
}

}
