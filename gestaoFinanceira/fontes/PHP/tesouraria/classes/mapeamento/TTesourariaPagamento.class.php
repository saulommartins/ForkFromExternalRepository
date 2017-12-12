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
    * Classe de mapeamento da tabela TESOURARIA_PAGAMENTO
    * Data de Criação: 21/10/2005

    * @author Analista: Lucas Leusin Oaigen
    * @author Desenvolvedor: Anderson R. M. Buzo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 32136 $
    $Name:  $
    $Autor:$
    $Date: 2007-10-18 12:08:09 -0200 (Qui, 18 Out 2007) $

    * Casos de uso: uc-02.04.05
*/

/*
$Log: TTesourariaPagamento.class.php,v $
Revision 1.20  2007/10/03 19:12:03  cako
Ticket#9496#

Revision 1.19  2007/09/14 20:56:57  cako
Ticket#9496#

Revision 1.17  2007/09/05 16:03:40  cako
Ticket#9496#

Revision 1.16  2007/06/25 19:17:28  luciano
Bug#9090#

Revision 1.15  2007/05/31 20:08:32  luciano
Bug#9090#

Revision 1.14  2007/01/17 18:11:38  luciano
Bug#7907#

Revision 1.13  2006/09/29 10:31:29  jose.eduardo
Bug#7060#

Revision 1.12  2006/09/27 17:39:22  jose.eduardo
Bug#7060#

Revision 1.11  2006/09/05 10:35:30  jose.eduardo
Bug#6741#

Revision 1.10  2006/07/05 20:38:38  cleisson
Adicionada tag Log aos arquivos

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  TESOURARIA_PAGAMENTO
  * Data de Criação: 31/10/2005

  * @author Analista: Lucas Leusin Oaigen
  * @author Desenvolvedor: Anderson R. M. Buzo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TTesourariaPagamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTesourariaPagamento()
{
    parent::Persistente();
    $this->setTabela("tesouraria.pagamento");

    $this->setCampoCod('');
    $this->setComplementoChave('exercicio,cod_nota,cod_entidade,timestamp');

    $this->AddCampo('exercicio'              , 'varchar'  , true , '04'  , true  , true  );
    $this->AddCampo('cod_entidade'           , 'integer'  , true , ''    , true  , true  );
    $this->AddCampo('cod_nota'               , 'integer'  , true , ''    , true  , true  );
    $this->AddCampo('timestamp'              , 'timestamp', true , ''    , true  , true  );
    $this->AddCampo('exercicio_boletim'      , 'varchar'  , true , '04'  , false , true  );
    $this->AddCampo('cod_autenticacao'       , 'integer'  , true , ''    , false , true  );
    $this->AddCampo('dt_autenticacao'        , 'date'     , true , ''    , false , true  );
    $this->AddCampo('cod_boletim'            , 'integer'  , true , ''    , false , true  );
    $this->AddCampo('cod_terminal'           , 'integer'  , true , ''    , false , true  );
    $this->AddCampo('timestamp_terminal'     , 'timestamp', true , ''    , false , true  );
    $this->AddCampo('cgm_usuario'            , 'integer'  , true , ''    , false , true  );
    $this->AddCampo('timestamp_usuario'      , 'timestamp', true , ''    , false , true  );
    $this->AddCampo('cod_plano'              , 'integer'  , true , ''    , false , true  );
    $this->AddCampo('exercicio_plano'        , 'varchar'  , true , '04'  , false , true  );
//  $this->AddCampo('cod_lote'               , 'integer'  , true , ''    , false , true  );
//  $this->AddCampo('tipo'                   , 'char'     , true , ''    , false , true  );
}

function montaRecuperaRelacionamento()
{
    $stSql = "    SELECT *
                    FROM tesouraria.fn_ordem_pagamento_estorno(  '" . $this->getDado('exercicio_boletim') . "'
                                                               , '" . $this->getDado('exercicio_empenho') . "'
                                                               , '" . $this->getDado('cod_entidade') . "'
                                                               , '" . $this->getDado('cod_ordem_inicial') . "'
                                                               , '" . $this->getDado('cod_ordem_final') . "'
                                                               , '" . $this->getDado('cod_empenho_inicial') . "'
                                                               , '" . $this->getDado('cod_empenho_final') . "'
                                                               , '" . $this->getDado('cod_nota_inicial') . "'
                                                               , '" . $this->getDado('cod_nota_final') . "'
                                                               , '" . $this->getDado('num_cgm') . "' ) AS tbl
                                                              (  exercicio            VARCHAR
                                                               , empenho_pagamento    VARCHAR
                                                               , exercicio_liquidacao VARCHAR
                                                               , exercicio_empenho    VARCHAR
                                                               , cod_entidade         INTEGER
                                                               , cod_empenho          INTEGER
                                                               , cod_nota             INTEGER
                                                               , empenho              VARCHAR
                                                               , ordem                VARCHAR
                                                               , nota                 VARCHAR
                                                               , beneficiario         VARCHAR
                                                               , vl_nota              DECIMAL(14,2)
                                                               , vl_ordem             DECIMAL(14,2)
                                                               , vl_prestado          DECIMAL(14,2)
                                                               , cod_conta            INTEGER
                                                               , nom_conta            VARCHAR
                                                              ) ";

    return $stSql;
}

function recuperaTipoOrdem(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaTipoOrdem();
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTipoOrdem()
{
    $stSql  = "SELECT EOP.cod_ordem                                                  \n";
    $stSql .= "      ,EOP.cod_entidade                                               \n";
    $stSql .= "      ,EOP.exercicio                                                  \n";
    $stSql .= "      ,EOP.tipo AS tipo_ordem                                         \n";
    $stSql .= "FROM empenho.ordem_pagamento AS EOP                                   \n";
    $stSql .= "WHERE EOP.exercicio    = '". $this->getDado("exercicio")    ."'       \n";
    $stSql .= "  AND EOP.cod_entidade =  ". $this->getDado("cod_entidade") ."        \n";
    $stSql .= "  AND EOP.cod_ordem    =  ". $this->getDado("cod_ordem")    ."        \n";

    return $stSql;
}

function recuperaCodPlanoRetencao(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCodPlanoRetencao();
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCodPlanoRetencao()
{
    $stSql = "SELECT lr.cod_plano
                    ,lr.cod_lote
                    ,nlp.cod_entidade
                    ,nlp.timestamp
                FROM empenho.nota_liquidacao_paga as nlp
                     JOIN contabilidade.pagamento as cp
                     ON (   cp.cod_nota   = nlp.cod_nota
                        AND cp.exercicio_liquidacao = nlp.exercicio
                        AND cp.cod_entidade = nlp.cod_entidade
                        AND cp.timestamp  = nlp.timestamp
                     )
                     JOIN contabilidade.lancamento_retencao as lr
                     ON (   lr.cod_lote     = cp.cod_lote
                        AND lr.cod_entidade = cp.cod_entidade
                        AND lr.sequencia    = cp.sequencia
                        AND lr.tipo         = cp.tipo
                        AND lr.exercicio    = cp.exercicio
                     )
               WHERE nlp.cod_nota = ".$this->getDado('cod_nota')."
                 AND nlp.cod_entidade = ".$this->getDado('cod_entidade')."
                 AND nlp.timestamp = '".$this->getDado('timestamp')."'
                 AND nlp.exercicio = '".$this->getDado('exercicio')."' ";

    return $stSql;
}

}
