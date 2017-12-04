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
    * Classe de mapeamento da tabela estagio.entidade_intermediadora
    * Data de Criação: 05/10/2006

    * @author Analista: Vandré Miguel Ramos
    * @author Desenvolvedor: Diego Lemos de Souza

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 30566 $
    $Name$
    $Author: andre $
    $Date: 2007-06-18 16:51:50 -0300 (Seg, 18 Jun 2007) $

    * Casos de uso: uc-04.07.01
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  estagio.entidade_intermediadora
  * Data de Criação: 05/10/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEstagioEntidadeIntermediadora extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEstagioEntidadeIntermediadora()
{
    parent::Persistente();
    $this->setTabela("estagio.entidade_intermediadora");

    $this->setCampoCod('');
    $this->setComplementoChave('numcgm');

    $this->AddCampo('numcgm'          ,'integer',true  ,''   ,true,'TCGMPessoaJuridica');
    $this->AddCampo('percentual_atual','numeric',true  ,'5,2',true);
}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT sw_cgm.numcgm                                                                                                    \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                                                   \n";
    $stSql .= "     , sw_cgm.tipo_logradouro||' '||sw_cgm.logradouro||', '||sw_cgm.numero||' '||sw_cgm.complemento AS endereco         \n";
    $stSql .= "     , sw_cgm.bairro                                                                                                    \n";
    $stSql .= "     , sw_municipio.nom_municipio                                                                                       \n";
    $stSql .= "     , sw_cgm.fone_comercial                                                                                            \n";
    $stSql .= "     , sw_cgm_instituicao.numcgm as numcgm_instituicao                                                                  \n";
    $stSql .= "     , sw_cgm_instituicao.nom_cgm as nom_cgm_instituicao                                                                \n";
    $stSql .= "  FROM sw_cgm                                                                                                           \n";
    $stSql .= "     , sw_municipio                                                                                                     \n";
    $stSql .= "     , estagio.entidade_intermediadora                                                                                  \n";
    $stSql .= "     , estagio.instituicao_entidade                                                                                     \n";
    $stSql .= "LEFT JOIN sw_cgm as sw_cgm_instituicao                                                                                  \n";
    $stSql .= "       ON sw_cgm_instituicao.numcgm = instituicao_entidade.cgm_instituicao                                              \n";
    $stSql .= " WHERE sw_cgm.cod_uf = sw_municipio.cod_uf                                                                              \n";
    $stSql .= "   AND sw_cgm.cod_municipio = sw_municipio.cod_municipio                                                                \n";
    $stSql .= "   AND sw_cgm.numcgm = entidade_intermediadora.numcgm                                                                   \n";
    $stSql .= "   AND instituicao_entidade.cgm_entidade = entidade_intermediadora.numcgm                                               \n";

    return $stSql;
}

function recuperaEntidadesIntermediarias(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? " ORDER BY ".$stOrdem : " ORDER BY sw_cgm.nom_cgm ";
    $stSql  = $this->montaRecuperaEntidadesIntermediarias().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEntidadesIntermediarias()
{
    $stSql .= "  SELECT sw_cgm.numcgm                                                                                                   \n";
    $stSql .= "       , sw_cgm.nom_cgm                                                                                                  \n";
    $stSql .= "    FROM sw_cgm                                                                                                          \n";
    $stSql .= "       , sw_cgm_pessoa_juridica                                                                                                          \n";
    $stSql .= "       , estagio.entidade_intermediadora                                                                                 \n";
    $stSql .= "   WHERE sw_cgm.numcgm = entidade_intermediadora.numcgm                                                                  \n";
    $stSql .= ( $this->getDado("numcgm") ) ? " AND entidade_intermediadora.numcgm = ".$this->getDado("numcgm")." \n" : "";
    $stSql .= "     AND sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm                                                                    \n";
//    $stSql .= "GROUP BY sw_cgm.numcgm                                                                                                   \n";
//    $stSql .= "       , sw_cgm.nom_cgm                                                                                                  \n";
    return $stSql;
}

function recuperaEstagiariosDaEntidade(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY sw_cgm.nom_cgm ";
    $stSql  = $this->montaRecuperaEstagiariosDaEntidade().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaEstagiariosDaEntidade()
{
    $stSql .= "SELECT estagiario_estagio.cgm_estagiario                                                            \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                               \n";
    $stSql .= "  FROM estagio.entidade_intermediadora                                                              \n";
    $stSql .= "     , estagio.entidade_intermediadora_estagio                                                      \n";
    $stSql .= "     , estagio.estagiario_estagio                                                                   \n";
    $stSql .= "     , estagio.estagiario                                                                           \n";
    $stSql .= "     , sw_cgm_pessoa_fisica                                                                         \n";
    $stSql .= "     , sw_cgm                                                                                       \n";
    $stSql .= " WHERE entidade_intermediadora.numcgm = entidade_intermediadora_estagio.cgm_entidade                \n";
    $stSql .= "   AND entidade_intermediadora_estagio.cgm_estagiario = estagiario_estagio.cgm_estagiario           \n";
    $stSql .= "   AND entidade_intermediadora_estagio.cgm_instituicao_ensino = estagiario_estagio.cgm_instituicao_ensino                           \n";
    $stSql .= "   AND entidade_intermediadora_estagio.cod_curso = estagiario_estagio.cod_curso                     \n";
    $stSql .= "   AND entidade_intermediadora_estagio.cod_estagio = estagiario_estagio.cod_estagio                 \n";
    $stSql .= "   AND estagiario_estagio.cgm_estagiario = estagiario.numcgm                                        \n";
    $stSql .= "   AND estagiario.numcgm = sw_cgm_pessoa_fisica.numcgm                                              \n";
    $stSql .= "   AND sw_cgm_pessoa_fisica.numcgm = sw_cgm.numcgm                                                  \n";

    return $stSql;
}

function recuperaInstituicoesDaEntidade(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY sw_cgm.nom_cgm ";
    $stSql  = $this->montaRecuperaInstituicoesDaEntidade().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaInstituicoesDaEntidade()
{
    $stSql .= "  SELECT sw_cgm.numcgm                                                                                                   \n";
    $stSql .= "       , sw_cgm.nom_cgm                                                                                                  \n";
    $stSql .= "    FROM sw_cgm                                                                                                          \n";
    $stSql .= "       , estagio.entidade_intermediadora                                                                                 \n";
    $stSql .= "       , estagio.instituicao_entidade                                                                                      \n";
    $stSql .= "   WHERE entidade_intermediadora.numcgm = instituicao_entidade.cgm_entidade                                      \n";
    $stSql .= "     AND instituicao_entidade.cgm_instituicao = sw_cgm.numcgm                                      \n";

    return $stSql;
}

}
?>
