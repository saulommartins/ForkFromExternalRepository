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
  * Classe de mapeamento da tabela ECONOMICO.CADASTRO_ECONOMICO_EMPRESA_FATO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMCadastroEconomicoEmpresaFato.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.11  2007/05/09 13:04:03  cercato
Bug #9247#

Revision 1.10  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.CADASTRO_ECONOMICO_EMPRESA_FATO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMCadastroEconomicoEmpresaFato extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMCadastroEconomicoEmpresaFato()
{
    parent::Persistente();
    $this->setTabela('economico.cadastro_economico_empresa_fato');

    $this->setCampoCod('inscricao_economica');
    $this->setComplementoChave('');

    $this->AddCampo('inscricao_economica','integer',true,'',true,true);
    $this->AddCampo('numcgm','integer',true,'',false,true);

}

function recuperaInscricao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaInscricao().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaInscricao()
{
    $stSql .= "SELECT                                                  \n";
    $stSql .= "    ce.inscricao_economica,                             \n";
    $stSql .= "    TO_CHAR(ce.dt_abertura,'dd/mm/yyyy') as dt_abertura,\n";
    $stSql .= "    ef.numcgm as numcgm,                                \n";
    $stSql .= "    rp.numcgm as resp_numcgm,                           \n";
    $stSql .= "    cgm.nom_cgm as resp_nomcgm,                         \n";
    $stSql .= "    rp.sequencia,                                       \n";
    $stSql .= "    cgm_empresa.nom_cgm                                 \n";

    $stSql .= "FROM                                                    \n";
    $stSql .= "    economico.cadastro_economico as ce                  \n";

    $stSql .= "INNER JOIN
                    (
                        SELECT
                            rp.*
                        FROM
                            economico.cadastro_econ_resp_contabil AS rp

                        INNER JOIN
                            (
                            SELECT
                                MAX(timestamp) AS timestamp,
                                inscricao_economica
                            FROM
                                economico.cadastro_econ_resp_contabil
                            GROUP BY
                                inscricao_economica
                            )as tmp
                        ON
                            tmp.inscricao_economica = rp.inscricao_economica
                            AND tmp.timestamp = rp.timestamp
                    )as rp
                ON
                    rp.inscricao_economica = ce.inscricao_economica    \n";

    $stSql .= "LEFT JOIN                                               \n";
    $stSql .= "    economico.baixa_cadastro_economico as ba            \n";
    $stSql .= "ON                                                      \n";
    $stSql .= "    ce.inscricao_economica = ba.inscricao_economica,     \n";

    $stSql .= "    economico.cadastro_economico_empresa_fato as ef,    \n";

    $stSql .= "    sw_cgm as cgm,                                      \n";
    $stSql .= "    (   SELECT                                          \n";
    $stSql .= "             nom_cgm, numcgm                            \n";
    $stSql .= "        FROM                                            \n";
    $stSql .= "             sw_cgm as cgm                              \n";
    $stSql .= "    ) as cgm_empresa                                    \n";

    $stSql .= "WHERE                                                   \n";
    $stSql .= "ce.inscricao_economica = ef.inscricao_economica   AND   \n";
    $stSql .= "cgm.numcgm     = rp.numcgm                        AND   \n";
    $stSql .= "cgm_empresa.numcgm     = ef.numcgm                AND   \n";
    $stSql .= "ba.inscricao_economica is null                          \n";

    return $stSql;
}

}
