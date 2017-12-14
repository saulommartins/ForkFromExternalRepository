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
    * Classe de mapeamento da tabela tcmgo.nota_fiscal
    * Data de Criação: 25/09/2008

    * @author Analista: Tonismar
    * @author Desenvolvedor: Leonard Spencer

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: $
    $Name$
    $Author: $
    $Date: $
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGONTF extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/

function recuperaNotas(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaNotas().$stFiltro;

//    $stSql .= "  GROUP BY nf.cod_nota                                            \n";
//    $stSql .= "         , nf.nro_nota                                            \n";
//    $stSql .= "         , nf.nro_serie                                           \n";
//    $stSql .= "         , nf.aidf                                                \n";
//    $stSql .= "         , nf.vl_nota                                             \n";
//    $stSql .= "         , nf.inscricao_municipal                                 \n";
//    $stSql .= "         , nf.inscricao_estadual                                  \n";
//    $stSql .= "         , nfe.cod_nota                                           \n";
//    $stSql .= "         , nfe.exercicio                                          \n";
//    $stSql .= "         , nfe.cod_entidade                                       \n";
//    $stSql .= "         , nfe.cod_empenho                                        \n";
//    $stSql .= "         , nfe.vl_associado                                       \n";
//    $stSql .= "         , od.cod_programa                                        \n";
//    $stSql .= "         , od.num_orgao                                           \n";
//    $stSql .= "         , od.num_unidade                                         \n";
//    $stSql .= "         , od.cod_funcao                                          \n";
//    $stSql .= "         , od.cod_subfuncao                                       \n";
//    $stSql .= "         , od.num_pao                                             \n";
//    $stSql .= "         , tdp.elemento                                           \n";
//    $stSql .= "         , ocd.elemento                                           \n";
//    $stSql .= "         , tdp.subelemento                                        \n";
//    $stSql .= "         , sc.nom_cgm                                             \n";
//    $stSql .= "         , sc.cpf_cnpj                                            \n";
//    $stSql .= "         , sc.cep                                                 \n";
//    $stSql .= "         , su.sigla_uf                                            \n";
//    $stSql .= "         , ee.dt_empenho                                          \n";
//    if (Sessao::getExercicio() > 2010) {
//        $stSql .= "         , ee.cod_empenho                                     \n";
//        $stSql .= "         , ee.cod_entidade                                    \n";
//        $stSql .= "         , nota_liquidacao.cod_nota                           \n";
//        $stSql .= "         , nota_liquidacao.exercicio_empenho                  \n";
//        $stSql .= "         , nota_liquidacao.dt_liquidacao                      \n";
//    } else {
//        $stSql .= "         , nf.data_emissao                                    \n";
//    }
//    $stSql .= "         , nf.cod_tipo                                            \n";
//

    $this->setDebug( $stSql );

    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql );

    return $obErro;
}

function montaRecuperaNotas()
{
    $stSql  = "    SELECT nota_fiscal.cod_nota                                              \n";
    $stSql .= "         , nota_fiscal.nro_nota                                              \n";
    if (Sessao::getExercicio() > 2010) {
        $stSql .= "     , TCMGO.numero_nota_liquidacao('".Sessao::getExercicio()."', empenho.cod_entidade, nota_liquidacao.cod_nota, nota_liquidacao.exercicio_empenho, empenho.cod_empenho) AS nro_liquidacao ";
        $stSql .= "     , nota_fiscal.cod_tipo  as tipo_docto ";
        $stSql .= "         , to_char(nota_liquidacao.dt_liquidacao,'dd/mm/yyyy') as data_emissao    \n";
    } else {
        $stSql .= "         , to_char(nota_fiscal.data_emissao,'dd/mm/yyyy') as data_emissao    \n";
    }
    $stSql .= "         , RPAD(nota_fiscal.nro_serie, 8, ' ') AS nro_serie                  \n";
    $stSql .= "         , RPAD(nota_fiscal.aidf, 8, ' ')      AS aidf                       \n";
    $stSql .= "         , nota_fiscal.vl_nota                                               \n";
    $stSql .= "         , nota_fiscal.inscricao_municipal                                   \n";
    $stSql .= "         , nota_fiscal.inscricao_estadual                                    \n";
    $stSql .= "         , nota_fiscal_exercicio.cod_nota                                    \n";
    $stSql .= "         , TO_CHAR(empenho.dt_empenho,'ddmmyyyy') as dt_empenho              \n";
    $stSql .= "         , nota_fiscal_exercicio.exercicio                                   \n";
    $stSql .= "         , nota_fiscal_exercicio.cod_entidade                                \n";
    $stSql .= "         , nota_fiscal_exercicio.cod_empenho                                 \n";
    $stSql .= "         , nota_fiscal_exercicio.vl_associado                                \n";
    $stSql .= "         , despesa.cod_programa                                              \n";
    $stSql .= "         , despesa.num_orgao                                                 \n";
    $stSql .= "         , despesa.num_unidade                                               \n";
    $stSql .= "         , despesa.cod_funcao                                                \n";
    $stSql .= "         , despesa.cod_subfuncao                                             \n";
    $stSql .= "         , substr(TO_CHAR(despesa.num_pao, '9999'), 3, 3) AS nro_proj_ativ   \n";
    $stSql .= "         , substr(TO_CHAR(despesa.num_pao, '9999'), 2, 1) AS natureza_acao   \n";
    $stSql .= "         , CASE WHEN elemento_de_para.elemento IS NULL THEN                  \n";
    $stSql .= "                    conta_despesa.elemento                                   \n";
    $stSql .= "                ELSE                                                         \n";
    $stSql .= "                    elemento_de_para.elemento                                \n";
    $stSql .= "           END as elemento                                                   \n";
    $stSql .= "         , CASE WHEN elemento_de_para.subelemento IS NULL THEN               \n";
    $stSql .= "                    '0'                                                      \n";
    $stSql .= "                ELSE                                                         \n";
    $stSql .= "                    elemento_de_para.subelemento                             \n";
    $stSql .= "           END as subelemento                                                \n";
    $stSql .= "         , RPAD(pessoa.nom_cgm,50,' ') AS nome_credor                        \n";
    $stSql .= "         , LPAD(pessoa.cpf_cnpj,14,0 ) AS cpf_cnpj                           \n";
    $stSql .= "         , LPAD(pessoa.cep,8) AS cep                                         \n";
    $stSql .= "         , sw_uf.sigla_uf                                                    \n";
    $stSql .= "         , 10 as tipo_registro                                               \n";
    $stSql .= "         , 0 as nro_sequencial                                               \n";

    if (Sessao::getExercicio() > 2010) {
        $stSql .= "      FROM tcmgo.nota_fiscal_empenho_liquidacao AS nota_fiscal_exercicio \n";
    } else {
        $stSql .= "      FROM tcmgo.nota_fiscal_empenho AS nota_fiscal_exercicio            \n";
    }

    $stSql .= "INNER JOIN tcmgo.nota_fiscal                                                 \n";
    $stSql .= "        ON nota_fiscal.cod_nota = nota_fiscal_exercicio.cod_nota             \n";
    $stSql .= "INNER JOIN empenho.empenho                                                   \n";
    $stSql .= "        ON empenho.cod_empenho  = nota_fiscal_exercicio.cod_empenho          \n";
    $stSql .= "       AND empenho.exercicio    = nota_fiscal_exercicio.exercicio            \n";
    $stSql .= "       AND empenho.cod_entidade = nota_fiscal_exercicio.cod_entidade         \n";

    if (Sessao::getExercicio() > 2010) {
        $stSql .= "INNER JOIN empenho.nota_liquidacao                                                   \n";
        $stSql .= "        ON nota_liquidacao.exercicio    = nota_fiscal_exercicio.exercicio_liquidacao \n";
        $stSql .= "       AND nota_liquidacao.cod_entidade = nota_fiscal_exercicio.cod_entidade         \n";
        $stSql .= "       AND nota_liquidacao.cod_nota     = nota_fiscal_exercicio.cod_nota_liquidacao  \n";
    }

    $stSql .= "INNER JOIN empenho.pre_empenho                                               \n";
    $stSql .= "        ON pre_empenho.cod_pre_empenho = empenho.cod_pre_empenho             \n";
    $stSql .= "       AND pre_empenho.exercicio       = empenho.exercicio                   \n";
    $stSql .= "INNER JOIN empenho.pre_empenho_despesa                                       \n";
    $stSql .= "        ON pre_empenho_despesa.cod_pre_empenho = pre_empenho.cod_pre_empenho \n";
    $stSql .= "       AND pre_empenho_despesa.exercicio       = pre_empenho.exercicio       \n";
    $stSql .= "INNER JOIN orcamento.despesa                                                 \n";
    $stSql .= "        ON despesa.cod_despesa = pre_empenho_despesa.cod_despesa             \n";
    $stSql .= "       AND despesa.exercicio   = pre_empenho_despesa.exercicio               \n";
    $stSql .= "INNER JOIN orcamento.pao                                                     \n";
    $stSql .= "        ON pao.num_pao   = despesa.num_pao                                   \n";
    $stSql .= "       AND pao.exercicio = despesa.exercicio                                 \n";
    $stSql .= "INNER JOIN ( SELECT *                                                        \n";
    $stSql .= "                  , substr(translate(cod_estrutural, '.', ''), 1, 6)         \n";
    $stSql .= "                 AS elemento                                                 \n";
    $stSql .= "               FROM orcamento.conta_despesa                                  \n";
    $stSql .= "         ) conta_despesa                                                     \n";
    $stSql .= "        ON conta_despesa.cod_conta = pre_empenho_despesa.cod_conta           \n";
    $stSql .= "       AND conta_despesa.exercicio = pre_empenho_despesa.exercicio           \n";
    $stSql .= "INNER JOIN ( SELECT sw_cgm.numcgm                                            \n";
    $stSql .= "                  , nom_cgm                                                  \n";
    $stSql .= "                  , cpf as cpf_cnpj                                          \n";
    $stSql .= "                  , cod_uf                                                   \n";
    $stSql .= "                  , cep                                                      \n";
    $stSql .= "                  , 1 as tipo_pessoa                                         \n";
    $stSql .= "               FROM sw_cgm                                                   \n";
    $stSql .= "          LEFT JOIN sw_cgm_pessoa_fisica                                     \n";
    $stSql .= "                 ON sw_cgm.numcgm = sw_cgm_pessoa_fisica.numcgm              \n";
    $stSql .= "              WHERE cpf IS NOT NULL                                          \n";
    $stSql .= "              UNION                                                          \n";
    $stSql .= "             SELECT sw_cgm.numcgm                                            \n";
    $stSql .= "                  , nom_cgm                                                  \n";
    $stSql .= "                  , cnpj as cpf_cnpj                                         \n";
    $stSql .= "                  , cod_uf                                                   \n";
    $stSql .= "                  , cep                                                      \n";
    $stSql .= "                  , 2 as tipo_pessoa                                         \n";
    $stSql .= "               FROM sw_cgm                                                   \n";
    $stSql .= "          LEFT JOIN sw_cgm_pessoa_juridica                                   \n";
    $stSql .= "                 ON sw_cgm.numcgm = sw_cgm_pessoa_juridica.numcgm            \n";
    $stSql .= "              WHERE cnpj IS NOT NULL                                         \n";
    $stSql .= "         ) pessoa                                                            \n";
    $stSql .= "        ON pessoa.numcgm = pre_empenho.cgm_beneficiario                      \n";
    $stSql .= "INNER JOIN sw_uf                                                             \n";
    $stSql .= "        ON sw_uf.cod_uf = pessoa.cod_uf                                      \n";
    $stSql .= " LEFT JOIN ( SELECT *                                                        \n";
    $stSql .= "                       , substr(translate(estrutural, '.', ''), 1, 6)        \n";
    $stSql .= "                      AS elemento                                            \n";
    $stSql .= "                       , substr(translate(estrutural, '.', ''), 7, 2)        \n";
    $stSql .= "                      AS subelemento                                         \n";
    $stSql .= "                    FROM tcmgo.elemento_de_para                              \n";
    $stSql .= "                ) elemento_de_para                                           \n";
    $stSql .= "        ON elemento_de_para.cod_conta = pre_empenho_despesa.cod_conta        \n";
    $stSql .= "       AND elemento_de_para.exercicio = pre_empenho_despesa.exercicio        \n";

    return $stSql;
}

}
