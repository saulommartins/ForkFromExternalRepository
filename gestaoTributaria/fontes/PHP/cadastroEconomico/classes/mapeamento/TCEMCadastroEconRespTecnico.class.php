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
  * Classe de mapeamento da tabela ECONOMICO.CADASTRO_ECON_RESP_TECNICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMCadastroEconRespTecnico.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.04
*/

/*
$Log$
Revision 1.8  2007/03/27 19:29:00  rodrigo
Bug #8768#

Revision 1.7  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.CADASTRO_ECON_RESP_TECNICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMCadastroEconRespTecnico extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMCadastroEconRespTecnico()
{
    parent::Persistente();
    $this->setTabela('economico.cadastro_econ_resp_tecnico');

    $this->setCampoCod('');
    $this->setComplementoChave('inscricao_economica,numcgm,sequencia');

    $this->AddCampo('inscricao_economica','integer'  ,true ,'',true ,true );
    $this->AddCampo('numcgm'             ,'integer'  ,false ,'',true ,true );
    $this->AddCampo('sequencia'          ,'integer'  ,false ,'',true ,true );
    $this->AddCampo('timestamp'          ,'timestamp',false,'',true ,false);
    $this->AddCampo('ativo'              ,'boolean'  ,true ,'',false,false);

}

function recuperaRelacionamentoCadastro(&$rsRecordSet, $stCondicao = "" , $stOrdem = "" , $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    if(trim($stOrdem))
        $stOrdem = (strpos($stOrdem,"ORDER BY")===false)?" ORDER BY $stOrdem":$stOrdem;
    $stSql = $this->montaRecuperaRelacionamentoCadastro().$stCondicao.$stOrdem;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaRelacionamentoCadastro()
{
    $stSql =" SELECT cadastro_econ_resp_tecnico.inscricao_economica                                 \n";
    $stSql.="       ,cadastro_econ_resp_tecnico.numcgm                                              \n";
    $stSql.="       ,cadastro_econ_resp_tecnico.sequencia                                           \n";
    $stSql.="       ,cadastro_econ_resp_tecnico.timestamp                                           \n";
    $stSql.="       ,responsavel_tecnico.num_registro                                               \n";
    $stSql.="       ,sw_cgm.nom_cgm                                                                 \n";
    $stSql.="       ,profissao.nom_profissao                                                        \n";
    $stSql.="       ,profissao.cod_profissao                                                        \n";
    $stSql.="       ,conselho.cod_conselho                                                          \n";
    $stSql.="       ,conselho.nom_conselho                                                          \n";
    $stSql.="       ,conselho.nom_registro                                                          \n";
    $stSql.="       ,sw_uf.sigla_uf                                                                 \n";
    $stSql.="   FROM cse.profissao                                                                  \n";
    $stSql.="       ,cse.conselho                                                                   \n";
    $stSql.="       ,sw_cgm                                                                         \n";
    $stSql.="       ,sw_uf                                                                          \n";
    $stSql.="       ,economico.cadastro_econ_resp_tecnico                                           \n";
    $stSql.="       ,economico.responsavel_tecnico                                                  \n";
    $stSql.="   LEFT JOIN (economico.responsavel_empresa JOIN economico.empresa_profissao           \n";
    $stSql.="                          ON(responsavel_empresa.numcgm = empresa_profissao.numcgm))   \n";
    $stSql.="      ON responsavel_tecnico.numcgm    = responsavel_empresa.numcgm_resp_tecnico       \n";
    $stSql.="         AND responsavel_tecnico.sequencia = responsavel_empresa.sequencia_resp_tecnico\n";
    $stSql.="       ,economico.responsavel                                                          \n";
    $stSql.="  WHERE sw_cgm.numcgm = responsavel.numcgm                                             \n";
    $stSql.="    AND sw_cgm.cod_uf             = sw_uf.cod_uf                                       \n";
    $stSql.="    AND((responsavel_tecnico.numcgm       = responsavel.numcgm                         \n";
    $stSql.="    AND responsavel_tecnico.sequencia     = responsavel.sequencia)                     \n";
    $stSql.="     OR(responsavel_empresa.numcgm        = responsavel.numcgm                         \n";
    $stSql.="    AND responsavel_empresa.sequencia     = responsavel.sequencia))                    \n";
    $stSql.="    AND responsavel_tecnico.cod_profissao = profissao.cod_profissao                    \n";
    $stSql.="    AND profissao.cod_conselho        = conselho.cod_conselho                          \n";
    $stSql.="    AND responsavel.numcgm                = cadastro_econ_resp_tecnico.numcgm          \n";
    $stSql.="    AND responsavel.sequencia             = cadastro_econ_resp_tecnico.sequencia       \n";
    $stSql.="    AND cadastro_econ_resp_tecnico.ativo  = true                                       \n";

  return $stSql;
}

function montaRecuperaRelacionamento()
{
    $stSql  = "SELECT                                            \n";
    $stSql .= "     cert.inscricao_economica,                    \n";
    $stSql .= "     cert.numcgm,                                 \n";
    $stSql .= "     cert.sequencia,                              \n"; //era profissao
    $stSql .= "     cert.timestamp,                              \n";
    $stSql .= "     rp.num_registro,                             \n";
    $stSql .= "     cgm.nom_cgm,                                 \n";
    $stSql .= "     pr.nom_profissao,                            \n";
    $stSql .= "     pr.cod_conselho,                             \n";
    $stSql .= "     pr.nom_conselho,                             \n";
    $stSql .= "     pr.nom_registro,                             \n";
    $stSql .= "     uf.sigla_uf                                  \n";
    $stSql .= "FROM                                              \n";
    $stSql .= "     economico.cadastro_econ_resp_tecnico AS cert \n";
    $stSql .= "JOIN                                              \n";
    $stSql .= "     economico.responsavel_tecnico AS rp          \n";
    $stSql .= "ON                                                \n";
    $stSql .= "     cert.numcgm        = rp.numcgm AND           \n";
    $stSql .= "     cert.sequencia     = rp.sequencia AND        \n"; //era profissao
    $stSql .= "     cert.ativo = true                            \n";
    $stSql .= "JOIN                                              \n";
    $stSql .= "     (                                            \n";
    $stSql .= "     SELECT                                       \n";
    $stSql .= "         p.cod_profissao,                         \n";
    $stSql .= "         p.nom_profissao,                         \n";
    $stSql .= "         c.cod_conselho,                          \n";
    $stSql .= "         c.nom_conselho,                          \n";
    $stSql .= "         c.nom_registro                           \n";
    $stSql .= "     FROM                                         \n";
    $stSql .= "         cse.profissao AS p                       \n";
    $stSql .= "     JOIN                                         \n";
    $stSql .= "         cse.conselho AS c                        \n";
    $stSql .= "     ON                                           \n";
    $stSql .= "         p.cod_conselho = c.cod_conselho          \n";
    $stSql .= "     ) AS pr                                      \n";
    $stSql .= "ON                                                \n";
    $stSql .= "     rp.cod_profissao = pr.cod_profissao          \n"; //cert.cod_profissao = pr.cod_profissao
    $stSql .= "JOIN                                              \n";
    $stSql .= "     sw_cgm cgm                                   \n";
    $stSql .= "ON                                                \n";
    $stSql .= "     cert.numcgm = cgm.numcgm                     \n";
    $stSql .= "JOIN                                              \n";
    $stSql .= "     sw_uf uf                                     \n";
    $stSql .= "ON                                                \n";
    $stSql .= "     rp.cod_uf = uf.cod_uf                        \n";

    return $stSql;
}

}
