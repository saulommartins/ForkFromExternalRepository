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
  * Classe de mapeamento da tabela ARRECADACAO.CADASTRO_ECONOMICO_FATURAMENTO
    * Data de Criação: 12/05/2005

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TARRCadastroEconomicoFaturamento.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.03.22
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ARRECADACAO.CADASTRO_ECONOMICO_FATURAMENTO
  * Data de Criação: 18/05/2005

  * @author Analista: Fabio Bertoldi
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TARRCadastroEconomicoFaturamento extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TARRCadastroEconomicoFaturamento()
{
    parent::Persistente();
    $this->setTabela('arrecadacao.cadastro_economico_faturamento');

    $this->setCampoCod('');
    $this->setComplementoChave('inscricao_economica,timestamp');

    $this->AddCampo('inscricao_economica','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('competencia','varchar',true,'7',false,false);
}

function recuperaAvaliarReceita(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaAvaliarReceita().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaAvaliarReceita()
{
    $stSql .= " SELECT                                                     \n";
    $stSql .= "     CE.INSCRICAO_ECONOMICA,                                \n";
    $stSql .= "     CG.NOM_CGM,                                            \n";
    $stSql .= "     CG.NUMCGM,                                             \n";
    $stSql .= "     LL.COD_LOCALIZACAO                                     \n";
    $stSql .= " FROM                                                       \n";
    $stSql .= "     imobiliario.imovel_lote          AS IL,                    \n";
    $stSql .= "     imobiliario.lote_localizacao     AS LL,                    \n";
    $stSql .= "     imobiliario.proprietario         AS PR,                    \n";
    $stSql .= "     economico.domicilio_fiscal     AS DF,                    \n";
    $stSql .= "     economico.cadastro_economico   AS CE,                    \n";
    $stSql .= "     economico.atividade_cadastro_economico AS AC,            \n";
    $stSql .= "     sw_cgm                      AS CG                     \n";
    $stSql .= " WHERE                                                      \n";
    $stSql .= "     IL.INSCRICAO_MUNICIPAL = PR.INSCRICAO_MUNICIPAL AND    \n";
    $stSql .= "     LL.COD_LOTE            = IL.COD_LOTE            AND    \n";
    $stSql .= "     IL.INSCRICAO_MUNICIPAL = DF.INSCRICAO_MUNICIPAL AND    \n";
    $stSql .= "     DF.INSCRICAO_ECONOMICA = CE.INSCRICAO_ECONOMICA AND    \n";
    $stSql .= "     AC.INSCRICAO_ECONOMICA = CE.INSCRICAO_ECONOMICA AND    \n";
    $stSql .= "     PR.NUMCGM              = CG.NUMCGM                     \n";

    return $stSql;
}

function recuperaTimestampCadastroEconomicoFaturamento(&$rsRecordSet, $stFiltro =
"", $stOrdem = " ORDER BY timestamp DESC LIMIT 1 ", $boTransacao = "") {
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaCadastroEconomicoFaturamento().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaCadastroEconomicoFaturamento()
{
    $stSql .= " SELECT                                                                                 \n";
    $stSql .= "     CEF.timestamp                                \n";
    $stSql .= " FROM                                                                                    \n";
    $stSql .= "     arrecadacao.cadastro_economico_faturamento AS CEF  \n";

    return $stSql;
}

function montaRecuperaRelacionamento()
{
    $stSQL  = " SELECT                                                                                                                                            \n";
    $stSQL .= "     CADASTRO_ECONOMICO.INSCRICAO_ECONOMICA,                                                                                                       \n";
    $stSQL .= "     TO_CHAR(CADASTRO_ECONOMICO.DT_ABERTURA,'DD/MM/YYYY') AS DT_ABERTURA,                                                                          \n";
    $stSQL .= "     COALESCE(CADASTRO_ECONOMICO_EMPRESA_DIREITO.NUMCGM, CADASTRO_ECONOMICO_EMPRESA_FATO.NUMCGM, CADASTRO_ECONOMICO_AUTONOMO.NUMCGM ) AS NUMCGM,   \n";
    $stSQL .= "     (SELECT                                                                                                                                       \n";
    $stSQL .= "         NOM_CGM                                                                                                                                   \n";
    $stSQL .= "      FROM                                                                                                                                         \n";
    $stSQL .= "         SW_CGM                                                                                                                                    \n";
    $stSQL .= "      WHERE                                                                                                                                        \n";
    $stSQL .= "         NUMCGM = COALESCE(CADASTRO_ECONOMICO_EMPRESA_DIREITO.NUMCGM, CADASTRO_ECONOMICO_EMPRESA_FATO.NUMCGM, CADASTRO_ECONOMICO_AUTONOMO.NUMCGM ) \n";
    $stSQL .= "     ) AS NOM_CGM,                                                                                                                                 \n";
    $stSQL .= "     CADASTRO_ECONOMICO_FATURAMENTO.COMPETENCIA,                                                                                                   \n";
    $stSQL .= "     CADASTRO_ECONOMICO_FATURAMENTO.TIMESTAMP                                                                                                      \n";
    $stSQL .= " FROM                                                                                                                                              \n";
    $stSQL .= "     ECONOMICO.CADASTRO_ECONOMICO                                                                                                                  \n";
    $stSQL .= " LEFT JOIN                                                                                                                                         \n";
    $stSQL .= "     ECONOMICO.CADASTRO_ECONOMICO_EMPRESA_DIREITO                                                                                                  \n";
    $stSQL .= " ON                                                                                                                                                \n";
    $stSQL .= "     CADASTRO_ECONOMICO.INSCRICAO_ECONOMICA = CADASTRO_ECONOMICO_EMPRESA_DIREITO.INSCRICAO_ECONOMICA                                               \n";
    $stSQL .= " LEFT JOIN                                                                                                                                         \n";
    $stSQL .= "     ECONOMICO.CADASTRO_ECONOMICO_EMPRESA_FATO                                                                                                     \n";
    $stSQL .= " ON                                                                                                                                                \n";
    $stSQL .= "     CADASTRO_ECONOMICO.INSCRICAO_ECONOMICA = CADASTRO_ECONOMICO_EMPRESA_FATO.INSCRICAO_ECONOMICA                                                  \n";
    $stSQL .= " LEFT JOIN                                                                                                                                         \n";
    $stSQL .= "     ECONOMICO.CADASTRO_ECONOMICO_AUTONOMO                                                                                                         \n";
    $stSQL .= " ON                                                                                                                                                \n";
    $stSQL .= "     CADASTRO_ECONOMICO.INSCRICAO_ECONOMICA = CADASTRO_ECONOMICO_AUTONOMO.INSCRICAO_ECONOMICA                                                      \n";
    $stSQL .= " INNER JOIN                                                                                                                                        \n";
    $stSQL .= "     ARRECADACAO.CADASTRO_ECONOMICO_FATURAMENTO                                                                                                    \n";
    $stSQL .= " ON                                                                                                                                                \n";
    $stSQL .= "     CADASTRO_ECONOMICO.INSCRICAO_ECONOMICA = CADASTRO_ECONOMICO_FATURAMENTO.INSCRICAO_ECONOMICA AND                                               \n";
    $stSQL .= "     TRIM(CADASTRO_ECONOMICO_FATURAMENTO.COMPETENCIA) != ''                                                                                        \n";
    $stSQL .= " WHERE                                                                                                                                             \n";
    $stSQL .= " EXISTS ( SELECT *                                                                                                                                 \n";
    $stSQL .= "          FROM                                                                                                                                     \n";
    $stSQL .= "              arrecadacao.faturamento_servico                                                                                                      \n";
    $stSQL .= "          WHERE                                                                                                                                    \n";
    $stSQL .= "              cadastro_economico_faturamento.inscricao_economica = faturamento_servico.inscricao_economica                                         \n";
    $stSQL .= "              AND cadastro_economico_faturamento.timestamp           = faturamento_servico.timestamp )                                             \n";

    return $stSQL;
}

}
?>
