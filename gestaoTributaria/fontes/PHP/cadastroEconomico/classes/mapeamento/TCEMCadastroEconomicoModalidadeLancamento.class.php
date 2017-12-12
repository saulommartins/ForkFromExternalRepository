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
  * Classe de mapeamento da tabela ECONOMICO.CADASTRO_ECONOMICO_MODALIDADE_LANCAMENTO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMCadastroEconomicoModalidadeLancamento.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.13
*/

/*
$Log$
Revision 1.7  2006/11/08 10:34:36  fabio
alteração do uc_05.02.13

Revision 1.6  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.CADASTRO_ECONOMICO_MODALIDADE_LANCAMENTO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMCadastroEconomicoModalidadeLancamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMCadastroEconomicoModalidadeLancamento()
{
    parent::Persistente();
    $this->setTabela('economico.cadastro_economico_modalidade_lancamento');

    $this->setCampoCod('');
    $this->setComplementoChave('cod_modalidade,cod_atividade,inscricao_economica,ocorrencia_atividade,dt_inicio');

    $this->AddCampo('cod_modalidade'      ,'integer'  ,true ,''    ,true ,true );
    $this->AddCampo('cod_atividade'       ,'integer'  ,true ,''    ,true ,true );
    $this->AddCampo('inscricao_economica' ,'integer'  ,true ,''    ,true ,true );
    $this->AddCampo('ocorrencia_atividade','integer'  ,true ,''    ,true ,true );
    $this->AddCampo('dt_inicio'           ,'date'     ,true ,''    ,true ,false);
    $this->AddCampo('dt_baixa'            ,'date'     ,false,''    ,false,false);
    $this->AddCampo('motivo_baixa'        ,'varchar'  ,false,''    ,false,false);
    $this->AddCampo('valor'               ,'numeric'  ,false,'14,2',false,false);
    $this->AddCampo('percentual'          ,'boolean'  ,false,''    ,false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                                                                  \n";
    $stSql .= "    DISTINCT ON(CEML.INSCRICAO_ECONOMICA)                               \n";
    $stSql .= "    CEML.INSCRICAO_ECONOMICA,                                           \n";
    $stSql .= "    CEML.COD_ATIVIDADE,                                                 \n";
    $stSql .= "    A.NOM_ATIVIDADE,                                                    \n";
    $stSql .= "    CEML.OCORRENCIA_ATIVIDADE,                                          \n";
    $stSql .= "    CEML.COD_MODALIDADE,                                                \n";
    $stSql .= "    EML.NOM_MODALIDADE,                                                 \n";
    $stSql .= "    TO_CHAR ( CEML.DT_INICIO,'dd/mm/yyyy' ) AS DT_VIGENCIA_MODALIDADE,  \n";
    $stSql .= "    TO_CHAR ( CEML.DT_BAIXA ,'dd/mm/yyyy' ) AS DT_BAIXA_MODALIDADE,     \n";
    $stSql .= "    CEML.MOTIVO_BAIXA AS MOTIVO_BAIXA_MODALIDADE,                       \n";
    $stSql .= "    CASE                                                                \n";
    $stSql .= "        WHEN                                                            \n";
    $stSql .= "            CEEF.INSCRICAO_ECONOMICA IS NOT NULL                        \n";
    $stSql .= "        THEN                                                            \n";
    $stSql .= "            CEEF.NUMCGM                                                 \n";
    $stSql .= "        WHEN                                                            \n";
    $stSql .= "            CEED.INSCRICAO_ECONOMICA IS NOT NULL                        \n";
    $stSql .= "        THEN                                                            \n";
    $stSql .= "            CEED.NUMCGM                                                 \n";
    $stSql .= "        WHEN                                                            \n";
    $stSql .= "            CEA.INSCRICAO_ECONOMICA IS NOT NULL                         \n";
    $stSql .= "        THEN                                                            \n";
    $stSql .= "            CEA.NUMCGM                                                  \n";
    $stSql .= "    END AS NUMCGM,                                                      \n";
    $stSql .= "    CGM.NOM_CGM                                                         \n";
    $stSql .= "FROM                                                                    \n";
    $stSql .= "  economico.cadastro_economico_modalidade_lancamento AS CEML              \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.modalidade_lancamento AS EML                                  \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    EML.COD_MODALIDADE = CEML.COD_MODALIDADE                            \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.cadastro_economico_empresa_fato AS CEEF                       \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    CEEF.INSCRICAO_ECONOMICA = CEML.INSCRICAO_ECONOMICA                 \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.cadastro_economico_empresa_direito AS CEED                    \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    CEED.INSCRICAO_ECONOMICA = CEML.INSCRICAO_ECONOMICA                 \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.cadastro_economico_autonomo AS CEA                            \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    CEA.INSCRICAO_ECONOMICA = CEML.INSCRICAO_ECONOMICA                  \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  sw_cgm AS CGM                                                     \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    CEA.NUMCGM = CGM.NUMCGM OR                                          \n";
    $stSql .= "    CEEF.NUMCGM = CGM.NUMCGM OR                                         \n";
    $stSql .= "    CEED.NUMCGM = CGM.NUMCGM                                            \n";
    $stSql .= "LEFT JOIN                                                               \n";
    $stSql .= "  economico.atividade AS A                                                \n";
    $stSql .= "ON                                                                      \n";
    $stSql .= "    A.COD_ATIVIDADE = CEML.COD_ATIVIDADE                                \n";

    return $stSql;
}

function recuperaModalidadeInscricao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaModalidadeInscricao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaModalidadeInscricao()
{
    $stSql  = "SELECT                                                     \n";
    $stSql .= "    CEML.INSCRICAO_ECONOMICA,                              \n";
    $stSql .= "    CEML.COD_ATIVIDADE,                                    \n";
    $stSql .= "    CEML.COD_MODALIDADE,                                   \n";
    $stSql .= "    CEML.OCORRENCIA_ATIVIDADE,                             \n";
    $stSql .= "    CEML.DT_INICIO                                         \n";
    $stSql .= "FROM                                                       \n";
    $stSql .= "  economico.cadastro_economico_modalidade_lancamento AS CEML \n";
    $stSql .= "WHERE                                                      \n";
    $stSql .= "    CEML.DT_BAIXA     IS NULL  AND                         \n";
    $stSql .= "    CEML.MOTIVO_BAIXA IS NULL                              \n";

    return $stSql;
}

function recuperaBaixados(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaBaixados().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaBaixados()
{
    $stSql  = "SELECT                                                     \n";
    $stSql .= "    CEML.DT_BAIXA,                                         \n";
    $stSql .= "    CEML.MOTIVO_BAIXA                                      \n";
    $stSql .= "FROM                                                       \n";
    $stSql .= "  economico.cadastro_economico_modalidade_lancamento AS CEML \n";

    return $stSql;
}

}
