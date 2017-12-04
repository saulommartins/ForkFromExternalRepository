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
    * Classe de mapeamento da tabela ARRECADACAO.IMOVEL_VALOR_VENAL
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRImovelVVenal.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.06
*/

/*
$Log$
Revision 1.33  2007/08/07 21:47:45  cercato
Bug#9848#

Revision 1.32  2007/05/08 19:20:03  cercato
Bug #8436#

Revision 1.31  2007/01/26 16:57:18  cercato
atualizado para os arquivos de mata

Revision 1.25  2006/09/19 17:01:08  fabio
#7012#

Revision 1.24  2006/09/19 16:21:57  fabio
#7012#

Revision 1.23  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.22  2006/09/15 10:41:36  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.IMOVEL_V_VENAL
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRImovelVVenal extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRImovelVVenal()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.imovel_v_venal');

    $this->setCampoCod('');
    $this->setComplementoChave('inscricao_municipal,timestamp');

    $this->AddCampo('inscricao_municipal','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('exercicio','char',true,'4',false,false);

    $this->AddCampo('venal_territorial_informado','numeric',false,'14,2',false,false);
    $this->AddCampo('venal_predial_informado','numeric',false,'14,2',false,false);
    $this->AddCampo('venal_total_informado','numeric',false,'14,2',false,false);

    $this->AddCampo('venal_territorial_calculado','numeric',false,'14,2',false,false);
    $this->AddCampo('venal_predial_calculado','numeric',false,'14,2',false,false);
    $this->AddCampo('venal_total_calculado','numeric',false,'14,2',false,false);

    $this->AddCampo('venal_territorial_declarado','numeric',false,'14,2',false,false);
    $this->AddCampo('venal_predial_declarado','numeric',false,'14,2',false,false);
    $this->AddCampo('venal_total_declarado','numeric',false,'14,2',false,false);

    $this->AddCampo('venal_territorial_avaliado','numeric',false,'14,2',false,false);
    $this->AddCampo('venal_predial_avaliado','numeric',false,'14,2',false,false);
    $this->AddCampo('venal_total_avaliado','numeric',false,'14,2',false,false);

    $this->AddCampo('valor_financiado','numeric',false,'14,2',false,false);
    $this->AddCampo('aliquota_valor_avaliado','numeric',false,'14,2',false,false);
    $this->AddCampo('aliquota_valor_financiado','numeric',false,'14,2',false,false);
}

function recuperaAvaliacaoImoveis(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAvaliacaoImoveis().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function recuperaAvaliacaoImoveisCalculado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAvaliacaoImoveisCalculado().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function recuperaAvaliacaoImoveisInformado(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAvaliacaoImoveisInformado().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaAvaliacaoImoveis()
{
    $stSql  = " SELECT                                              \n";
    $stSql .= "     IV.*                         \n";
    $stSql .= " FROM                                                \n";
    $stSql .= "     arrecadacao.imovel_v_venal AS IV,               \n";
    $stSql .= "     ( SELECT                                        \n";
    $stSql .= "         INSCRICAO_MUNICIPAL,                        \n";
    $stSql .= "         MAX(TIMESTAMP) AS TIMESTAMP                 \n";
    $stSql .= "       FROM                                          \n";
    $stSql .= "         arrecadacao.imovel_v_venal                  \n";
    $stSql .= "       GROUP BY INSCRICAO_MUNICIPAL                  \n";
    $stSql .= "     ) as miv                                        \n";
    $stSql .= " WHERE                                               \n";
    $stSql .= "     miv.timestamp = iv.timestamp                    \n";
    $stSql .= "     AND miv.INSCRICAO_MUNICIPAL = iv.INSCRICAO_MUNICIPAL \n";

    return $stSql;
}

function montaRecuperaAvaliacaoImoveisCalculado()
{
    $stSql  = " SELECT  DISTINCT                                    \n";
    $stSql .= "     IV.INSCRICAO_MUNICIPAL,                         \n";
    $stSql .= "     IV.venal_territorial_calculado,                 \n";
    $stSql .= "     IV.venal_predial_calculado,                     \n";
    $stSql .= "     IV.venal_total_calculado,                       \n";
    $stSql .= "     IV.timestamp                                    \n";
    $stSql .= " FROM                                                \n";
    $stSql .= "     arrecadacao.imovel_v_venal AS IV ,              \n";
    $stSql .= "     ( SELECT                                        \n";
    $stSql .= "         INSCRICAO_MUNICIPAL,                        \n";
    $stSql .= "         MAX(TIMESTAMP) AS TIMESTAMP                 \n";
    $stSql .= "       FROM                                          \n";
    $stSql .= "         arrecadacao.imovel_v_venal                  \n";
    $stSql .= "       GROUP BY INSCRICAO_MUNICIPAL                  \n";
    $stSql .= "     ) as miv                                        \n";
    $stSql .= " WHERE                                               \n";
    $stSql .= "     miv.timestamp = iv.timestamp                    \n";
    $stSql .= "     AND miv.INSCRICAO_MUNICIPAL = iv.INSCRICAO_MUNICIPAL \n";

    return $stSql;
}

function montaRecuperaAvaliacaoImoveisInformado()
{
    $stSql  = " SELECT  DISTINCT                                    \n";
    $stSql .= "     IV.INSCRICAO_MUNICIPAL,                         \n";
    $stSql .= "     IV.venal_territorial_informado,                 \n";
    $stSql .= "     IV.venal_predial_informado,                     \n";
    $stSql .= "     IV.venal_total_informado,                       \n";
    $stSql .= "     IV.timestamp                                    \n";
    $stSql .= " FROM                                                \n";
    $stSql .= "     arrecadacao.imovel_v_venal AS IV,              \n";
    $stSql .= "     ( SELECT                                        \n";
    $stSql .= "         INSCRICAO_MUNICIPAL,                        \n";
    $stSql .= "         MAX(TIMESTAMP) AS TIMESTAMP                 \n";
    $stSql .= "       FROM                                          \n";
    $stSql .= "         arrecadacao.imovel_v_venal                  \n";
    $stSql .= "       GROUP BY INSCRICAO_MUNICIPAL                  \n";
    $stSql .= "     ) as miv                                        \n";
    $stSql .= " WHERE                                                  \n";
    $stSql .= "     miv.timestamp = iv.timestamp                    \n";
    $stSql .= "     AND miv.INSCRICAO_MUNICIPAL = iv.INSCRICAO_MUNICIPAL \n";

    return $stSql;
}

function recuperaImoveisNaoLancados(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = " \n order by cota desc ";
    $stSql = $this->montaRecuperaImoveisNaoLancados().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaImoveisNaoLancados()
{
    $stSql  = "       select ii.inscricao_municipal                                                                                            \n";
    $stSql .= "            , arrecadacao.fn_consulta_endereco_imovel(ii.inscricao_municipal) as dados           \n";
    $stSql .= "            , cgm.numcgm                                                                                                            \n";
    $stSql .= "            , cgm.nom_cgm                                                                                                           \n";
    $stSql .= "            , ip.cota                                                                                                                      \n";
    $stSql .= "         from imobiliario.imovel ii                                                                                                 \n";
    $stSql .= "   inner join (    select inscricao_municipal                                                                               \n";
    $stSql .= "                        , max(timestamp)                                                                                            \n";
    $stSql .= "                     from arrecadacao.imovel_v_venal                                                                       \n";
    $stSql .= "                 group by inscricao_municipal) aiv                                                                           \n";
    $stSql .= "           on aiv.inscricao_municipal = ii.inscricao_municipal                                                       \n";
    $stSql .= "  INNER JOIN ( select inscricao_municipal \n";
    $stSql .= "                 , max(numcgm)  as numcgm                                                                               \n";
    $stSql .= "                 , max(cota) as cota                                                                                    \n";
    $stSql .= "            from imobiliario.proprietario                                                                               \n";
    $stSql .= "        group by inscricao_municipal                                                                                    \n";
    $stSql .= "             ) ip on  ip.inscricao_municipal = ii.inscricao_municipal        \n";
    $stSql .= "  INNER JOIN sw_cgm cgm ON cgm.numcgm = ip.numcgm                                                         \n";

    return $stSql;
}

function recuperaVenaisImovelConsulta(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaVenaisImoveisConsulta().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaVenaisImoveisConsulta()
{
    $stSql  = " SELECT                                                                                  \n";
    $stSql .= "     IV.INSCRICAO_MUNICIPAL,                                                   \n";
    $stSql .= "     to_char(IV.TIMESTAMP,'dd/mm/YYYY hh:mm') as data,       \n";
    $stSql .= "     case when IV.VENAL_TOTAL_INFORMADO IS NOT NULL then \n";
    $stSql .= "         IV.VENAL_TERRITORIAL_INFORMADO \n";
    $stSql .= "     else \n";
    $stSql .= "         IV.VENAL_TERRITORIAL_CALCULADO \n";
    $stSql .= "     end AS venal_territorial, \n";

    $stSql .= "     case when IV.VENAL_TOTAL_INFORMADO IS NOT NULL then \n";
    $stSql .= "         IV.VENAL_PREDIAL_INFORMADO \n";
    $stSql .= "     else \n";
    $stSql .= "         IV.VENAL_PREDIAL_CALCULADO \n";
    $stSql .= "     end AS venal_predial, \n";

    $stSql .= "     case when IV.VENAL_TOTAL_INFORMADO IS NOT NULL then \n";
    $stSql .= "         IV.VENAL_TOTAL_INFORMADO \n";
    $stSql .= "     else \n";
    $stSql .= "         IV.VENAL_TOTAL_CALCULADO \n";
    $stSql .= "     end AS venal_total, \n";

    $stSql .= "     case \n";
    $stSql .= "         when IV.VENAL_TOTAL_INFORMADO IS NOT NULL then \n";
    $stSql .= "             'Informado'::varchar  \n";
    $stSql .= "         else                      \n";
    $stSql .= "             'Calculado'::varchar  \n";
    $stSql .= "     end as tipo,                  \n";
    $stSql .= "     IV.EXERCICIO                  \n";
    $stSql .= " FROM                              \n";
    $stSql .= "     arrecadacao.imovel_v_venal AS IV  \n";

    return $stSql;
}

function recuperaMensagemItbi(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "", $inCodLancamento ="", $inInscricao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaMensagemItbi($inCodLancamento, $inInscricao).$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}
function montaRecuperaMensagemItbi($inCodLancamento, $inInscricao)
{
    $stSql  = "    select *                                                                                                                 \n";
    $stSql .= "         , arrecadacao.fn_busca_valor_avaliado_itbi(transferencia_imovel.inscricao_municipal) as base_calculo                  \n";
    $stSql .= "         , case when  ( select count(1) from imobiliario.transferencia_adquirente where cod_transferencia = transferencia_imovel.cod_transferencia) > 1 then \n";
    $stSql .= "                ( select nom_cgm from sw_cgm where numcgm = transferencia_adquirente.numcgm)::varchar||' E OUTROS'           \n";
    $stSql .= "           else                                                                                                              \n";
    $stSql .= "                ( select nom_cgm from sw_cgm where numcgm = transferencia_adquirente.numcgm)::varchar                        \n";
    $stSql .= "           end as adquirinte                                                                                                 \n";
    $stSql .= "         , arrecadacao.fn_busca_valor_financiado_itbi(transferencia_imovel.inscricao_municipal)   \n";
    $stSql .= "             as valor_financiado                                                                                             \n";
    $stSql .= "         , arrecadacao.fn_busca_valor_declarado_itbi(transferencia_imovel.inscricao_municipal)     \n";
    $stSql .= "             as valor_pactuado,
                         transferencia_adquirente.cota \n";
    $stSql .= "      from imobiliario.transferencia_imovel                                                                                  \n";
    $stSql .= "inner join imobiliario.natureza_transferencia                                                                                \n";
    $stSql .= "        on transferencia_imovel.cod_natureza = imobiliario.natureza_transferencia.cod_natureza                               \n";
    $stSql .= " left join imobiliario.transferencia_processo                                                                                \n";
    $stSql .= "        on transferencia_imovel.cod_transferencia = transferencia_processo.cod_transferencia                                 \n";
    $stSql .= "         , imobiliario.transferencia_adquirente                                                                              \n";
    $stSql .= "     where transferencia_adquirente.cod_transferencia = transferencia_imovel.cod_transferencia                               \n";
    /*if ($inCodLancamento) {
        $stSql .= " AND transferencia_imovel.dt_cadastro = (
                                                            SELECT timestamp
                                                            FROM arrecadacao.imovel_calculo
                                                            WHERE imovel_calculo.inscricao_municipal = ".$inInscricao."
                                                              AND imovel_calculo.cod_calculo = (SELECT cod_calculo
                                                                                                FROM arrecadacao.lancamento_calculo
                                                                                                WHERE lancamento_calculo.cod_lancamento = ".$inCodLancamento."
                                                                                                )
                                                            )
                    \n";
    }*/

    return $stSql;
}

function recuperaAvaliacaoImoveisCalculadoNaoNulo(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAvaliacaoImoveisCalculadoNaoNulo().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaAvaliacaoImoveisCalculadoNaoNulo()
{
    $stSql  = " SELECT  DISTINCT \n";
    $stSql .= "     IV.INSCRICAO_MUNICIPAL, \n";
    $stSql .= "     IV.venal_territorial_calculado, \n";
    $stSql .= "     IV.venal_predial_calculado, \n";
    $stSql .= "     IV.venal_total_calculado, \n";
    $stSql .= "     IV.timestamp \n";
    $stSql .= " FROM \n";
    $stSql .= "     arrecadacao.imovel_v_venal AS IV, \n";
    $stSql .= "     ( SELECT \n";
    $stSql .= "         INSCRICAO_MUNICIPAL, \n";
    $stSql .= "         MAX(TIMESTAMP) AS TIMESTAMP \n";
    $stSql .= "       FROM \n";
    $stSql .= "         arrecadacao.imovel_v_venal \n";
    $stSql .= "       WHERE \n";
    $stSql .= "         venal_total_calculado IS NOT NULL \n";
    $stSql .= "       GROUP BY INSCRICAO_MUNICIPAL \n";
    $stSql .= "     ) as miv \n";
    $stSql .= " WHERE \n";
    $stSql .= "     miv.timestamp = iv.timestamp \n";
    $stSql .= "     AND miv.INSCRICAO_MUNICIPAL = iv.INSCRICAO_MUNICIPAL \n";

    return $stSql;
}

}
?>
