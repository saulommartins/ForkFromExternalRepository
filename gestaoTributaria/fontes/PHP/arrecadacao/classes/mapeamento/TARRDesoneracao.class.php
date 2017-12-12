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
    * Classe de mapeamento da tabela ARRECADACAO.DESONERACAO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRDesoneracao.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.04
*/

/*
$Log$
Revision 1.8  2006/09/15 11:50:01  fabio
corrigidas tags de caso de uso

Revision 1.7  2006/09/15 10:40:57  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ARRECADACAO.DESONERACAO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRDesoneracao extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRDesoneracao()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.desoneracao');

    $this->setCampoCod('cod_desoneracao');
    $this->setComplementoChave('');

    $this->AddCampo('cod_desoneracao','integer',true,'',true,false);
    $this->AddCampo('cod_credito','integer',true,'',false,true);
    $this->AddCampo('cod_natureza','integer',true,'',false,true);
    $this->AddCampo('cod_genero','integer',true,'',false,true);
    $this->AddCampo('cod_especie','integer',true,'',false,true);
    $this->AddCampo('cod_tipo_desoneracao','integer',true,'',false,true);
    $this->AddCampo('inicio','date',true,'',false,false);
    $this->AddCampo('termino','date',true,'',false,false);
    $this->AddCampo('expiracao','date',true,'',false,false);
    $this->AddCampo('prorrogavel','boolean',true,'',false,false);
    $this->AddCampo('revogavel','boolean',true,'',false,false);
    $this->AddCampo('cod_funcao','integer',true,'',false,true);
    $this->AddCampo('cod_modulo','integer',true,'',false,true);
    $this->AddCampo('cod_biblioteca','integer',true,'',false,true);
    $this->AddCampo('fundamentacao_legal','integer',true,'',false,true);

}

function recuperaDesoneracaoCredito(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDesoneracaoCredito().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaDesoneracaoCredito()
{
    $stSql .= "SELECT                                                                  \n";
    $stSql .= "    DE.COD_DESONERACAO,                                                 \n";
    $stSql .= "    TO_CHAR ( DE.INICIO,    'dd/mm/yyyy' )  AS DATA_INICIO,             \n";
    $stSql .= "    TO_CHAR ( DE.TERMINO,   'dd/mm/yyyy' )  AS DATA_TERMINO,            \n";
    $stSql .= "    TO_CHAR ( DE.EXPIRACAO, 'dd/mm/yyyy' )  AS DATA_EXPIRACAO,          \n";
    $stSql .= "    DE.FUNDAMENTACAO_LEGAL,                                             \n";
    $stSql .= "    DE.COD_BIBLIOTECA,                                                  \n";
    $stSql .= "    DE.COD_MODULO,                                                      \n";
    $stSql .= "    DE.COD_FUNCAO,                                                      \n";
    $stSql .= "    DE.PRORROGAVEL,                                                     \n";
    $stSql .= "    DE.REVOGAVEL,                                                       \n";
    $stSql .= "    TD.COD_TIPO_DESONERACAO,                                            \n";
    $stSql .= "    TD.DESCRICAO AS DESCRICAO_TIPO,                                     \n";
    $stSql .= "    CR.COD_CREDITO,                                                     \n";
    $stSql .= "    CR.DESCRICAO_CREDITO,                                               \n";
    $stSql .= "    CR.COD_NATUREZA,                                                    \n";
    $stSql .= "    CR.COD_GENERO,                                                      \n";
    $stSql .= "    CR.COD_ESPECIE,                                                     \n";
    $stSql .= "    AD.NUMCGM,                                                          \n";
    $stSql .= "    AD.NOM_CGM,                                                         \n";
    $stSql .= "    AD.OCORRENCIA,                                                      \n";
    $stSql .= "    TO_CHAR ( AD.DATA_CONCESSAO, 'dd/mm/yyyy' )  AS DATA_CONCESSAO,     \n";
    $stSql .= "    TO_CHAR ( AD.DATA_PRORROGACAO, 'dd/mm/yyyy' )  AS DATA_PRORROGACAO, \n";
    $stSql .= "    TO_CHAR ( AD.DATA_REVOGACAO, 'dd/mm/yyyy' )  AS DATA_REVOGACAO      \n";
    $stSql .= "FROM                                                                    \n";
    $stSql .= "    arrecadacao.tipo_desoneracao     AS TD,                                 \n";
    $stSql .= "    arrecadacao.desoneracao          AS DE                                  \n";
    $stSql .= "     LEFT JOIN                                                          \n";
    $stSql .= "        (SELECT                                                         \n";
    $stSql .= "            DA.*,                                                       \n";
    $stSql .= "            CGM.NOM_CGM                                                 \n";
    $stSql .= "         FROM                                                           \n";
    $stSql .= "            arrecadacao.desonerado   AS DA,                                 \n";
    $stSql .= "            sw_cgm              AS CGM                                 \n";
    $stSql .= "         WHERE                                                          \n";
    $stSql .= "            DA.NUMCGM = CGM.NUMCGM                                      \n";
    $stSql .= "        ) AS AD                                                         \n";
    $stSql .= "     ON                                                                 \n";
    $stSql .= "     AD.COD_DESONERACAO = DE.COD_DESONERACAO,                           \n";
    $stSql .= "    monetario.credito              AS CR                                \n";
    $stSql .= "WHERE                                                                   \n";
    $stSql .= "    DE.COD_TIPO_DESONERACAO = TD.COD_TIPO_DESONERACAO AND               \n";
    $stSql .= "    DE.COD_CREDITO          = CR.COD_CREDITO  AND                        \n";
    $stSql .= "    DE.COD_NATUREZA         = CR.COD_NATUREZA AND                       \n";
    $stSql .= "    DE.COD_GENERO           = CR.COD_GENERO   AND                       \n";
    $stSql .= "    DE.COD_ESPECIE          = CR.COD_ESPECIE                            \n";

    return $stSql;
}

function recuperaDesoneracaoLS(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDesoneracaoLS().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaDesoneracaoLS()
{
    $stSql  = " SELECT
                     DE.COD_DESONERACAO,
                     DE.cod_tipo_desoneracao,
                     TO_CHAR ( DE.INICIO,    'dd/mm/yyyy' )  AS DATA_INICIO,
                     TO_CHAR ( DE.TERMINO,   'dd/mm/yyyy' )  AS DATA_TERMINO,
                     TO_CHAR ( DE.EXPIRACAO, 'dd/mm/yyyy' )  AS DATA_EXPIRACAO,
                     DE.FUNDAMENTACAO_LEGAL,
                     DE.COD_BIBLIOTECA,
                     DE.COD_MODULO,
                     DE.COD_FUNCAO,
                     DE.PRORROGAVEL,
                     DE.REVOGAVEL,
                     CR.descricao_credito,
                     CR.COD_CREDITO,
                     CR.DESCRICAO_CREDITO,
                     CR.COD_NATUREZA,
                     CR.COD_GENERO,
                     CR.COD_ESPECIE,
                     TD.DESCRICAO AS DESCRICAO_TIPO

                FROM
                    arrecadacao.desoneracao          AS DE

                INNER JOIN
                    arrecadacao.tipo_desoneracao     AS TD
                ON
                    DE.COD_TIPO_DESONERACAO = TD.COD_TIPO_DESONERACAO

                INNER JOIN
                    monetario.credito              AS CR
                ON
                    DE.COD_CREDITO          = CR.COD_CREDITO  AND
                    DE.COD_NATUREZA         = CR.COD_NATUREZA AND
                    DE.COD_GENERO           = CR.COD_GENERO   AND
                    DE.COD_ESPECIE          = CR.COD_ESPECIE ";

    return $stSql;
}

function recuperaDesoneracaoCreditoPopup(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaDesoneracaoCreditoPopup().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaDesoneracaoCreditoPopup()
{
    $stSql = " SELECT DISTINCT
        DE.COD_DESONERACAO,
        TD.DESCRICAO AS DESCRICAO_TIPO,
        CR.COD_CREDITO,
        CR.DESCRICAO_CREDITO
    FROM
        arrecadacao.tipo_desoneracao     AS TD,
        arrecadacao.desoneracao          AS DE
        LEFT JOIN
            (SELECT
                DA.*,
                CGM.NOM_CGM
            FROM
                arrecadacao.desonerado   AS DA,
                sw_cgm              AS CGM
            WHERE
                DA.NUMCGM = CGM.NUMCGM
            ) AS AD
        ON
        AD.COD_DESONERACAO = DE.COD_DESONERACAO,
        monetario.credito              AS CR
    WHERE
        DE.COD_TIPO_DESONERACAO = TD.COD_TIPO_DESONERACAO AND
        DE.COD_CREDITO          = CR.COD_CREDITO  AND
        DE.COD_NATUREZA         = CR.COD_NATUREZA AND
        DE.COD_GENERO           = CR.COD_GENERO   AND
        DE.COD_ESPECIE          = CR.COD_ESPECIE ";

    return $stSql;
}

}
?>
