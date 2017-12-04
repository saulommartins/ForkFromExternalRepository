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
    * Classe de mapeamento da tabela licitacao.edital_impugnado
    * Data de Criação: 15/09/2006

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Nome do Programador

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 23163 $
    $Name$
    $Author: hboaventura $
    $Date: 2007-06-11 16:01:06 -0300 (Seg, 11 Jun 2007) $

    * Casos de uso: uc-03.05.27
*/
/*
$Log$
Revision 1.5  2007/06/11 18:59:43  hboaventura
Bug #9146#

Revision 1.4  2006/11/27 12:01:23  hboaventura
Implementação do caso de uso 03.05.27

Revision 1.2  2006/11/08 10:51:42  larocca
Inclusão dos Casos de Uso

Revision 1.1  2006/09/15 12:05:59  cleisson
inclusão

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  licitacao.edital_impugnado
  * Data de Criação: 15/09/2006

  * @author Analista: Gelson W. Gonçalves
  * @author Desenvolvedor: Nome do Programador

  * @package URBEM
  * @subpackage Mapeamento
*/
class TLicitacaoEditalImpugnado extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TLicitacaoEditalImpugnado()
{
    parent::Persistente();
    $this->setTabela("licitacao.edital_impugnado");

    $this->setCampoCod('');
     $this->setComplementoChave('num_edital,exercicio,exercicio_processo,cod_processo');

    $this->AddCampo('num_edital'        ,'integer',false ,''   ,true,'TLicitacaoEdital');
    $this->AddCampo('exercicio'         ,'varchar'   ,false ,'4'  ,true,'TLicitacaoEdital');
    $this->AddCampo('exercicio_processo','char'   ,false ,'4'  ,true,'TProtocoloProcesso','ano_exercicio');
    $this->AddCampo('cod_processo'      ,'integer',false ,''   ,true,'TProtocoloProcesso');

}

function recuperaProcessos(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaProcessos();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function recuperaProcessoEditalImpugnado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaProcessoEditalImpugnado();
    //echo $stSql;die;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function recuperaImpugnacaoAnulada(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaImpugnacaoAnulada();
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

}

function montaRecuperaProcessos()
{
    $stSql = "   SELECT edital_impugnado.exercicio_processo                            \r\n";
    $stSql.= "        , edital_impugnado.cod_processo                                  \r\n";
    $stSql.= "        , anulacao_impugnacao_edital.parecer_juridico   \r\n";
    $stSql.= "     FROM licitacao.edital_impugnado                    \r\n";
    $stSql.= "LEFT JOIN licitacao.anulacao_impugnacao_edital          \r\n";
    $stSql.= "       ON (licitacao.anulacao_impugnacao_edital.num_edital = licitacao.edital_impugnado.num_edital                  \r\n";
    $stSql.= "      AND licitacao.anulacao_impugnacao_edital.exercicio = licitacao.edital_impugnado.exercicio                     \r\n";
    $stSql.= "      AND licitacao.anulacao_impugnacao_edital.exercicio_processo = licitacao.edital_impugnado.exercicio_processo   \r\n";
    $stSql.= "      AND licitacao.anulacao_impugnacao_edital.cod_processo = licitacao.edital_impugnado.cod_processo )             \r\n";
    $stSql.= "    WHERE edital_impugnado.exercicio  = '".$this->getDado('exercicio')."'  \r\n";
    $stSql.= "      AND edital_impugnado.num_edital = ".$this->getDado('num_edital')." \r\n";
    $stSql.= "  ORDER BY cod_processo                                 \r\n";

    return $stSql;
}

function montaRecuperaProcessoEditalImpugnado()
{
    $stSql = "   SELECT num_edital                                                      \r\n";
    $stSql.= "        , exercicio                                                       \r\n";
    $stSql.= "     FROM licitacao.edital_impugnado                                      \r\n";
    $stSql.= "    WHERE num_edital <> ".$this->getDado('num_edital')."                  \r\n";
    $stSql.= "      AND exercicio = ".$this->getDado('exercicio')."                     \r\n";
    $stSql.= "      AND cod_processo = ".$this->getDado('cod_processo')."               \r\n";
    $stSql.= "      AND exercicio_processo = ".$this->getDado('exercicio_processo')."   \r\n";

    return $stSql;

}

function montaRecuperaImpugnacaoAnulada()
{
    $stSql = "   SELECT exercicio_processo                                                                                                            \r\n";
    $stSql.= "        , cod_processo                                                                                                                  \r\n";
    $stSql.= "     FROM licitacao.edital_impugnado                                                                                                    \r\n";
    $stSql.= "    WHERE exercicio  =  '". $this->getDado('exercicio') ."'                                                                               \r\n";
    $stSql.= "      AND num_edital =  ". $this->getDado('num_edital') ."                                                                              \r\n";
    $stSql.= "      AND NOT EXISTS ( SELECT cod_processo                                                                                              \r\n";
    $stSql.= "                         FROM licitacao.anulacao_impugnacao_edital                                                                      \r\n";
    $stSql.= "                        WHERE licitacao.anulacao_impugnacao_edital.num_edital = licitacao.edital_impugnado.num_edital                   \r\n";
    $stSql.= "                          AND licitacao.anulacao_impugnacao_edital.exercicio = licitacao.edital_impugnado.exercicio                     \r\n";
    $stSql.= "                          AND licitacao.anulacao_impugnacao_edital.exercicio_processo = licitacao.edital_impugnado.exercicio_processo   \r\n";
    $stSql.= "                          AND licitacao.anulacao_impugnacao_edital.cod_processo = licitacao.edital_impugnado.cod_processo )             \r\n";
    $stSql.= "  ORDER BY cod_processo                                                                                                                 \r\n";

    return $stSql;

}

    public function recuperaEditalImpugnacao(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaEditalImpugnacao",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }

    public function montaRecuperaEditalImpugnacao()
    {
        $stSql  = " SELECT
                            le.num_edital,
                            cp.descricao,
                            le.exercicio,
                            le.cod_entidade,
                            ll.cod_licitacao||'/'||ll.exercicio as num_licitacao,
                            ll.cod_entidade,
                            cgm.nom_cgm as entidade,
                            ll.cod_modalidade,
                            ll.cod_licitacao,
                            ll.cod_processo,
                            ll.exercicio_processo,
                            le.cod_modalidade,
                            ll.cod_mapa,
                            ll.exercicio_mapa,
                            mapa.cod_tipo_licitacao
                      FROM  licitacao.edital as le
                INNER JOIN  licitacao.licitacao as ll
                        ON  ll.cod_licitacao = le.cod_licitacao
                       AND  ll.cod_modalidade = le.cod_modalidade
                       AND  ll.cod_entidade = le.cod_entidade
                       AND  ll.exercicio = le.exercicio
                INNER JOIN  compras.mapa
                        ON  mapa.cod_mapa = ll.cod_mapa
                       AND  mapa.exercicio = ll.exercicio_mapa
                INNER JOIN  compras.modalidade as cp
                        ON  cp.cod_modalidade = ll.cod_modalidade
                INNER JOIN  orcamento.entidade as oe
                        ON  oe.cod_entidade = ll.cod_entidade
                       AND  oe.exercicio = ll.exercicio
                INNER JOIN  sw_cgm as cgm
                        ON  cgm.numcgm = oe.numcgm
                     WHERE  NOT	EXISTS(	SELECT 	1
                                           FROM 	licitacao.cotacao_licitacao
                                          WHERE  cotacao_licitacao.cod_licitacao = ll.cod_licitacao
                                            AND 	cotacao_licitacao.cod_modalidade = ll.cod_modalidade
                                            AND  cotacao_licitacao.cod_entidade = ll.cod_entidade
                                            AND  cotacao_licitacao.exercicio_licitacao = ll.exercicio
                                        ) 	AND
                              diff_datas_em_dias(now()::date,le.dt_abertura_propostas::date) > 2 AND
        ";
        if ( $this->getDado( 'num_edital' ) ) {
            $stSql .= " le.num_edital = ". $this->getDado( 'num_edital' )." and ";
        }

        if ( $this->getDado( 'exercicio_licitacao' ) ) {
            $stSql .= " le.exercicio_licitacao = '". $this->getDado( 'exercicio_licitacao' )."' and ";
        }

        if ( $this->getDado( 'cod_entidade' ) ) {
            $stSql .= " le.cod_entidade in ( ". $this->getDado( 'cod_entidade' )." ) and ";
        }

        if ( $this->getDado( 'cod_modalidade' ) ) {
            $stSql .= " le.cod_modalidade = ". $this->getDado( 'cod_modalidade' ). " and ";
        }

        if ( $this->getDado( 'cod_licitacao' ) ) {
            $stSql .= " le.cod_licitacao = ". $this->getDado( 'cod_licitacao' ). " and ";
        }

        if ( $this->getDado( 'cod_processo' ) ) {
            $stSql .= "ll.cod_processo = ". $this->getDado( 'cod_processo' ). " and ";
        }

        if ( $this->getDado( 'cod_mapa' ) ) {
            $stSql .= "ll.cod_mapa = ". $this->getDado( 'cod_mapa' ). " and ";
        }

        if ( $this->getDado( 'cod_tipo_licitacao' ) ) {
            $stSql .= "ll.cod_tipo_licitacao = ". $this->getDado( 'cod_tipo_licitacao' ). " and ";
        }

        if ( $this->getDado( 'cod_criterio' ) ) {
            $stSql .= "ll.cod_criterio = ". $this->getDado( 'cod_criterio' ). " and ";
        }

        if ( $this->getDado( 'cod_objeto' ) ) {
            $stSql .= "ll.cod_objeto = ". $this->getDado( 'cod_objeto' ). " and ";
        }

        if ( $this->getDado( 'cod_comissao' ) ) {
            $stSql .= "ll.cod_comissao = ". $this->getDado( 'cod_comissao' ). " and ";
        }

        $stSql .= " NOT EXISTS (   SELECT  1
                                     FROM  licitacao.edital_anulado
                                    WHERE  edital_anulado.num_edital = le.num_edital
                                      AND  edital_anulado.exercicio = le.exercicio
                               )
        ";

        return $stSql;

    }

}
