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
    * Classe de mapeamento da tabela compras.fornecedor
    * Data de Criação: 30/06/2006

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Leandro André Zis

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 25574 $
    $Name$
    $Author: andre.almeida $
    $Date: 2007-09-20 11:47:06 -0300 (Qui, 20 Set 2007) $

    * Casos de uso: uc-03.04.03
*/

/*
$Log$
Revision 1.10  2007/09/20 14:47:06  andre.almeida
Ticket#10222#

Revision 1.9  2006/11/09 15:16:14  hboaventura
bug #7372#

Revision 1.8  2006/09/29 17:35:31  fernando
implementado a alteração do UC-03.04.03

Revision 1.7  2006/09/25 17:59:15  fernando
método para retornar os dados do fornecedor na lista de alteração

Revision 1.6  2006/09/14 09:11:23  cleisson
Criação do componente fornecedor

Revision 1.5  2006/09/14 09:05:18  cleisson
Criação do componente fornecedor

Revision 1.4  2006/07/06 14:05:54  diego
Retirada tag de log com erro.

Revision 1.3  2006/07/06 12:11:10  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.fornecedor
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasFornecedor extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TComprasFornecedor()
{
    parent::Persistente();
    $this->setTabela("compras.fornecedor");

    $this->setCampoCod('');
    $this->setComplementoChave('cgm_fornecedor');

    $this->AddCampo('cgm_fornecedor','INTEGER',true,true,'',true,false,false,'TCGM');
    $this->AddCampo('vl_minimo_nf','NUMERIC',true,'14.2',false,false);
    $this->AddCampo('ativo','BOOLEAN','true','',false,false);
    $this->AddCampo('tipo','varchar',true,'1',false,false);

}
function montaRecuperaRelacionamento()
{
    $stSql  = " SELECT  * ";
    $stSql .= " FROM    compras.fornecedor as forn, sw_cgm as cgm";
    $stSql .= " WHERE cgm.numcgm = forn.cgm_fornecedor ";

    return $stSql;
}

function recuperaFornecedorCnpj(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaFornecedorCnpj",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaFornecedorCnpj()
{
    $stSql = "
        select sw_cgm_pessoa_juridica.cnpj
             , sw_cgm_pessoa_juridica.numcgm
          from compras.fornecedor
          join sw_cgm_pessoa_juridica
            on sw_cgm_pessoa_juridica.numcgm = fornecedor.cgm_fornecedor
            ";
    if ( $this->getDado('cnpj') ) {
        $stSql .= " where sw_cgm_pessoa_juridica.cnpj = '".$this->getDado('cnpj')."'";
    }

    return $stSql;
}

function recuperaListaFornecedor(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaFornecedor().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
}

function montaRecuperaListaFornecedor()
{
$stSql ="SELECT                                                                                        \n";
$stSql .="    cgm.nom_cgm                                                                               \n";
$stSql .="    ,f.cgm_fornecedor          \n";
$stSql .="    ,CASE WHEN\n";
$stSql .="        ((fi.timestamp_fim is  null AND fi.timestamp_inicio is null) OR (fi.timestamp_fim is not null ))\n";
$stSql .="    THEN                                                           \n";
$stSql .="        'Ativo'                                                                             \n";
$stSql .="    ELSE                                                                                      \n";
$stSql .="        'Inativo'                                                                               \n";
$stSql .="    END as status                                                                             \n";
$stSql .="    ,fi.motivo                                                                                \n";
$stSql .="    ,a.nom_atividade                                                                          \n";
$stSql .="    ,cf.cod_catalogo                                                                          \n";
$stSql .="    ,cf.cod_classificacao                                                                     \n";
$stSql .="    ,f.vl_minimo_nf                                                                           \n";
$stSql .="    ,f.tipo                                                                                   \n";
$stSql .="FROM                                                                                          \n";
$stSql .="    sw_cgm as cgm                                                                             \n";
$stSql .="    ,compras.fornecedor as f                                                                  \n";
$stSql .="    LEFT JOIN  \n";
$stSql .="        compras.fornecedor_classificacao as cf \n";
$stSql .="    ON \n";
$stSql .="        f.cgm_fornecedor = cf.cgm_fornecedor\n";
$stSql .="    LEFT JOIN \n";
$stSql .="        compras.fornecedor_atividade as ca \n";
$stSql .="    ON \n";
$stSql .="        f.cgm_fornecedor = ca.cgm_fornecedor    \n";
$stSql .="    LEFT JOIN \n";
$stSql .="        economico.atividade as a \n";
$stSql .="  ON \n";
$stSql .="        ca.cod_atividade = a.cod_atividade                \n";
$stSql .="    LEFT JOIN (SELECT\n";
$stSql .="                   coalesce(cfi.cgm_fornecedor,null) as cgm_fornecedor\n";
$stSql .="                  ,cfi.timestamp_inicio\n";
$stSql .="                  ,cfi.timestamp_fim\n";
$stSql .="                  ,cfi.motivo       \n";
$stSql .="               FROM\n";
$stSql .="                  compras.fornecedor_inativacao as cfi\n";
$stSql .="                  ,(SELECT        \n";
$stSql .="                       max(timestamp_inicio) as timestamp_inicio\n";
$stSql .="                       ,cgm_fornecedor\n";
$stSql .="                     FROM\n";
$stSql .="                        compras.fornecedor_inativacao\n";
$stSql .="                     GROUP BY\n";
$stSql .="                        cgm_fornecedor\n";
$stSql .="                    ) as ativacao\n";
$stSql .="                                         \n";
$stSql .="               WHERE\n";
$stSql .="                        ativacao.cgm_fornecedor = cfi.cgm_fornecedor                               \n";
$stSql .="                   AND  ativacao.timestamp_inicio = cfi.timestamp_inicio\n";
$stSql .="              ) as fi \n";
$stSql .="    ON \n";
$stSql .="        fi.cgm_fornecedor = f.cgm_fornecedor\n";
$stSql .="WHERE                                                                                         \n";
$stSql .="            cgm.numcgm = f.cgm_fornecedor\n";
if( $this->getDado('cgm_fornecedor') )
    $stSql .="  AND f.cgm_fornecedor = ".$this->getDado('cgm_fornecedor')."                             \n";
if( $this->getDado('cod_catalogo') )
    $stSql .="  AND cf.cod_catalogo = ".$this->getDado('cod_catalogo')."                                \n";
if( $this->getDado('cod_classificacao') )
    $stSql .="  AND cf.cod_classificacao = ".$this->getDado('cod_classificacao')."                      \n";
if( $this->getDado('cod_atividade') )
    $stSql .="  AND ca.cod_atividade = ".$this->getDado('cod_atividade')."                              \n";
if ($this->getDado('status')) {
    if ($this->getDado('status') == 'ativo') {
      $stSql .="AND ((fi.timestamp_fim is  null AND fi.timestamp_inicio is null) OR (fi.timestamp_fim is not null ))\n";
    } else {
        $stSql .="AND (fi.timestamp_fim is  null AND fi.timestamp_inicio is not null) \n";
    }
}
return $stSql;

}

/**
 * Função para Exportação de dados do MANAD.
 *
 * @param  Object  $rsRecordSet Objeto RecordSet
 * @param  String  $stCondicao  String de condição do SQL (WHERE)
 * @param  Boolean $boTransacao
 * @return Object  Objeto Erro
 */
function recuperaDadosMANAD(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro();
    $obConexao   = new Conexao();
    $rsRecordSet = new RecordSet();

    $stSql = $this->montaRecuperaDadosMANAD();
    $this->setDebug($stSql);
    //$this->debug();    exit();
    $obErro = $obConexao->executaSQL($rsRecordSet, $stSql, $boTransacao);

    return $obErro;
}

function montaRecuperaDadosMANAD()
{
$stSql ="    SELECT 'L750' as reg         \n";
$stSql .="          , ".$this->getDado('stExercicio')." as exerc  \n";
$stSql .="          , fornecedor.cgm_fornecedor as cod_fornecedor                                                                                                                      \n";
$stSql .="          , sw_cgm.nom_cgm as nom_fornecedor                                                                                                                          \n";
$stSql .="          , fornecedor.cgm_fornecedor                                                                                                                                         \n";
$stSql .="          , CASE WHEN ((sw_cgm_pessoa_juridica.cnpj is null))                                           \n";
$stSql .="          THEN                                                                                                                                                                              \n";
$stSql .="            '1'                                                                                                                                                                           \n";
$stSql .="          ELSE                                                                                                                                                                              \n";
$stSql .="            '2'                                                                                                                                                                        \n";
$stSql .="          END as tipo_fornecedor                                                                              \n";
$stSql .="          , CASE WHEN                                                                                                                                                                  \n";
$stSql .="          ((fi.timestamp_fim is  null AND fi.timestamp_inicio is null) OR (fi.timestamp_fim is not null ))                                           \n";
$stSql .="          THEN                                                                                                                                                                              \n";
$stSql .="            'Ativo'                                                                                                                                                                           \n";
$stSql .="          ELSE                                                                                                                                                                              \n";
$stSql .="            'Inativo'                                                                                                                                                                        \n";
$stSql .="          END as status                                                                              \n";
$stSql .="          , fi.motivo                                                                                 \n";
$stSql .="          , atividade.nom_atividade                                                                           \n";
$stSql .="          , fornecedor_classificacao.cod_catalogo                                                                           \n";
$stSql .="          , fornecedor_classificacao.cod_classificacao                                                                      \n";
$stSql .="          , sw_cgm_pessoa_juridica.cnpj as cnpj_fornecedor                                                                             \n";
$stSql .="          , sw_cgm_pessoa_fisica.cpf as cpf_fornecedor                                                                             \n";
$stSql .="          , sw_cgm.logradouro||', '||sw_cgm.numero||' '||sw_cgm.complemento||', bairro '||sw_cgm.bairro as end_fornecedor \n";
$stSql .="          , sw_municipio.nom_municipio as cid_fornecedor \n";
$stSql .="          , sw_uf.sigla_uf as uf_fornecedor \n";
$stSql .="          , sw_cgm.cep as cep_fornecedor \n";
$stSql .="          , catalogo_classificacao.descricao   as desc_tip_forn     \n";
$stSql .="          , fornecedor_documentos.num_documento as nit_fornecedor                       \n";
$stSql .="      FROM compras.fornecedor  \n";

$stSql .="INNER JOIN sw_cgm \n";
$stSql .="        ON sw_cgm.numcgm = fornecedor.cgm_fornecedor \n";

$stSql .=" LEFT JOIN sw_cgm_pessoa_fisica                                                                 \n";
$stSql .="        ON sw_cgm_pessoa_fisica.numcgm = fornecedor.cgm_fornecedor \n";

$stSql .=" LEFT JOIN sw_cgm_pessoa_juridica                                                                 \n";
$stSql .="        ON sw_cgm_pessoa_juridica.numcgm = fornecedor.cgm_fornecedor \n";

$stSql .=" LEFT JOIN sw_municipio \n";
$stSql .="        ON sw_municipio.cod_municipio = sw_cgm.cod_municipio \n";
$stSql .="       AND sw_municipio.cod_uf = sw_cgm.cod_uf \n";

$stSql .=" LEFT JOIN sw_uf \n";
$stSql .="        ON sw_uf.cod_uf = sw_municipio.cod_uf \n";

$stSql .=" LEFT JOIN compras.fornecedor_classificacao   \n";
$stSql .="        ON fornecedor.cgm_fornecedor = fornecedor_classificacao.cgm_fornecedor \n";

$stSql .=" LEFT JOIN almoxarifado.catalogo_classificacao \n";
$stSql .="        ON catalogo_classificacao.cod_classificacao = fornecedor_classificacao.cod_classificacao \n";
$stSql .="       AND catalogo_classificacao.cod_catalogo = fornecedor_classificacao.cod_catalogo  \n";

$stSql .=" LEFT JOIN compras.fornecedor_atividade  \n";
$stSql .="        ON fornecedor.cgm_fornecedor = fornecedor_atividade.cgm_fornecedor   \n";

$stSql .=" LEFT JOIN economico.atividade  \n";
$stSql .="        ON fornecedor_atividade.cod_atividade = atividade.cod_atividade         \n";

$stSql .=" LEFT JOIN (SELECT coalesce(cfi.cgm_fornecedor,null) as cgm_fornecedor  \n";
$stSql .="                 , cfi.timestamp_inicio  \n";
$stSql .="                 , cfi.timestamp_fim  \n";
$stSql .="                 , cfi.motivo        \n";
$stSql .="              FROM compras.fornecedor_inativacao as cfi \n";
$stSql .="                 , (SELECT max(timestamp_inicio) as timestamp_inicio  \n";
$stSql .="                         , cgm_fornecedor  \n";
$stSql .="                      FROM compras.fornecedor_inativacao \n";
$stSql .="                  GROUP BY cgm_fornecedor  \n";
$stSql .="                    ) as ativacao  \n";

$stSql .="               WHERE ativacao.cgm_fornecedor = cfi.cgm_fornecedor                                \n";
$stSql .="                 AND ativacao.timestamp_inicio = cfi.timestamp_inicio  \n";
$stSql .="              ) as fi  \n";
$stSql .="       ON  fi.cgm_fornecedor = fornecedor.cgm_fornecedor  \n";
$stSql .=" LEFT JOIN (SELECT num_documento,  cgm_fornecedor  \n";
$stSql .="              FROM licitacao.certificacao_documentos  \n";
$stSql .="             WHERE certificacao_documentos.cod_documento = ".$this->getDado('stDocINSS')."  \n";
$stSql .="           ) as fornecedor_documentos  \n";
$stSql .="        ON fornecedor_documentos.cgm_fornecedor = fornecedor.cgm_fornecedor \n";

$stSql .="    WHERE sw_cgm.numcgm = fornecedor.cgm_fornecedor   \n";

    return $stSql;
}

 function recuperaFornecedorDebito(&$rsRecordSet, $stFiltroSQL = "", $boTransacao = "")
 {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql  = $this->montaRecuperaFornecedorDebito( $stFiltroSQL );
        $this->setDebug($stSql);
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaFornecedorDebito($stFiltroSQL)
    {
        $stSql  ="    SELECT DISTINCT                                                                           \n";
        $stSql .="           parcela.cod_lancamento                                                             \n";
        $stSql .="         , lancamento_cgm.numcgm                                                              \n";
        $stSql .="      FROM arrecadacao.parcela                                                                \n";
        $stSql .="      JOIN ( SELECT DISTINCT                                                                  \n";
        $stSql .="                    lancamento_calculo.cod_lancamento,                                        \n";
        $stSql .="                    calculo_cgm.numcgm                                                        \n";
        $stSql .="               FROM arrecadacao.lancamento_calculo                                            \n";
        $stSql .="               JOIN arrecadacao.calculo_cgm                                                   \n";
        $stSql .="                 ON calculo_cgm.cod_calculo = lancamento_calculo.cod_calculo                  \n";
        $stSql .="                          ". $stFiltroSQL ."                                                      \n";
        $stSql .="           ) AS lancamento_cgm                                                                \n";
        $stSql .="        ON lancamento_cgm.cod_lancamento = parcela.cod_lancamento                             \n";
        $stSql .="      JOIN arrecadacao.carne                                                                  \n";
        $stSql .="        ON carne.cod_parcela = parcela.cod_parcela                                            \n";
        $stSql .=" LEFT JOIN arrecadacao.pagamento                                                              \n";
        $stSql .="        ON pagamento.numeracao = carne.numeracao                                              \n";
        $stSql .=" LEFT JOIN arrecadacao.carne_devolucao                                                        \n";
        $stSql .="        ON carne_devolucao.numeracao = carne.numeracao                                        \n";
        $stSql .=" WHERE                                                                                        \n";
        $stSql .="     carne_devolucao.numeracao IS NULL                                                        \n";
        $stSql .="     AND pagamento.numeracao IS NULL                                                          \n";
        $stSql .="     AND now()::date > parcela.vencimento                                                     \n";
        $stSql .="     AND parcela.valor > 0                                                                    \n";

        return $stSql;
    }

}
