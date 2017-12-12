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
    * Classe de mapeamento do Demonstratio das operações de crédito
    * Data de Criação: 15/08/2006

    * @author Analista: Cleisson Barboza
    * @author Desenvolvedor: Rodrigo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 59612 $
    $Name$
    $Autor:$
    $Date: 2014-09-02 09:00:51 -0300 (Tue, 02 Sep 2014) $

    * Casos de uso: uc-06.01.23
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class FSTNRGFAnexo4 extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/

function FSTNRGFAnexo4()
{
    parent::Persistente();
}

function recuperaDadosRelatorioAnexo4Cabecalho(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperaDadosRelatorioAnexo4Cabecalho();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosRelatorioAnexo4Cabecalho()
{
   $stSql.="    SELECT nom_municipio                                                                                \n";
   $stSql.="          ,( SELECT CASE WHEN parametro = 'cod_entidade_camara'     THEN 'LEGISLATIVO'                  \n";
   $stSql.="                         WHEN parametro = 'cod_entidade_prefeitura' THEN 'EXECUTIVO'                    \n";
   $stSql.="                    END AS parametro                                                                    \n";
   $stSql.="               FROM administracao.configuracao                                                          \n";
   $stSql.="              WHERE exercicio   = '".$this->getDado("stExercicio")."'                                   \n";
   $stSql.="                AND valor IN ( TO_NUMBER( '".$this->getDado("stEntidade")."' ,0 ) )                     \n";
   $stSql.="                AND ( parametro = 'cod_entidade_camara'                                                 \n";
   $stSql.="                 OR   parametro = 'cod_entidade_prefeitura' )                                           \n";
   $stSql.="              LIMIT 1                                                                                   \n";
   $stSql.="           ) AS parametro                                                                               \n";
   $stSql.="          ,CASE WHEN CHAR_LENGTH( '".$this->getDado("stEntidade" )."') = 1 THEN                         \n";
   $stSql.="           ( SELECT sw_cgm.nom_cgm                                                                      \n";
   $stSql.="               FROM orcamento.entidade                                                                  \n";
   $stSql.="                   ,sw_cgm                                                                              \n";
   $stSql.="              WHERE CAST( entidade.cod_entidade AS VARCHAR ) = '".$this->getDado("stEntidade")."'       \n";
   $stSql.="                AND entidade.numcgm                          = sw_cgm.numcgm                            \n";
   $stSql.="                AND entidade.exercicio                       = '".$this->getDado("stExercicio")."')     \n";
   $stSql.="           ELSE (SELECT sw_cgm.nom_cgm                                                                  \n";
   $stSql.="                   FROM administracao.configuracao                                                      \n";
   $stSql.="                       ,orcamento.entidade                                                              \n";
   $stSql.="                       ,sw_cgm                                                                          \n";
   $stSql.="                  WHERE configuracao.parametro = 'cod_entidade_prefeitura'                              \n";
   $stSql.="                    AND configuracao.exercicio = '".$this->getDado("stExercicio")."'                    \n";
   $stSql.="                    AND entidade.cod_entidade  = configuracao.valor                                     \n";
   $stSql.="                    AND entidade.numcgm        = sw_cgm.numcgm                                          \n";
   $stSql.="                    AND entidade.exercicio     = configuracao.exercicio )                               \n";
   $stSql.="           END AS esfera                                                                                \n";
   $stSql.="      FROM sw_municipio                                                                                 \n";
   $stSql.="     WHERE cod_municipio = ( SELECT valor                                                               \n";
   $stSql.="                               FROM administracao.configuracao                                          \n";
   $stSql.="                              WHERE exercicio = '".$this->getDado("stExercicio")."'                     \n";
   $stSql.="                                AND parametro = 'cod_municipio' )                                       \n";
   $stSql.="                                AND cod_uf    = ( SELECT valor                                          \n";
   $stSql.="                                                    FROM administracao.configuracao                     \n";
   $stSql.="                                                   WHERE exercicio = '".$this->getDado("stExercicio")."'\n";
   $stSql.="                                                     AND parametro = 'cod_uf'                           \n";
   $stSql.="                                                 )                                                      \n";

   return $stSql;
}

function recuperaDadosRelatorioAnexo4(&$rsRecordSet, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql  = $this->montaRecuperaDadosRelatorioAnexo4();
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaDadosRelatorioAnexo4()
{
    $stSql .= "    SELECT nivel                                                               \n";
    $stSql .= "          ,item                                                                \n";
    $stSql .= "          ,valor                                                               \n";
    $stSql .= "          ,linha                                                               \n";
    $stSql .= "      FROM stn.fn_rgf_anexo4( '".$this->getDado("stExercicio"   )."'           \n";
    $stSql .= "                             ,".$this->getDado("stQuadrimestre")."             \n";
    $stSql .= "                             ,'".$this->getDado("stEntidade"    )."') AS tabela\n";
    $stSql .= "                            ( nivel integer                                    \n";
    $stSql .= "                             ,item  varchar                                    \n";
    $stSql .= "                             ,valor numeric                                    \n";
    $stSql .= "                             ,linha char )                                     \n";

    return $stSql;
}

}
