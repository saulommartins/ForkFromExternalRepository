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
    * Classe de mapeamento da tabela estagio.instituicao_ensino
    * Data de Criação: 04/10/2006

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
  * Efetua conexão com a tabela  estagio.instituicao_ensino
  * Data de Criação: 04/10/2006

  * @author Analista: Vandré Miguel Ramos
  * @author Desenvolvedor: Diego Lemos de Souza

  * @package URBEM
  * @subpackage Mapeamento
*/
class TEstagioInstituicaoEnsino extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TEstagioInstituicaoEnsino()
{
    parent::Persistente();
    $this->setTabela("estagio.instituicao_ensino");

    $this->setCampoCod('');
    $this->setComplementoChave('numcgm');

    $this->AddCampo('numcgm','integer',true  ,'',true,'TCGMPessoaJuridica');

}

function montaRecuperaRelacionamento()
{
    $stSql .= "SELECT sw_cgm_pessoa_juridica.numcgm                                                                                    \n";
    $stSql .= "     , sw_cgm.nom_cgm                                                                                                   \n";
    $stSql .= "     , sw_cgm_pessoa_juridica.cnpj                                                                                      \n";
    $stSql .= "     , sw_cgm.tipo_logradouro||' '||sw_cgm.logradouro||', '||sw_cgm.numero||' '||sw_cgm.complemento AS endereco         \n";
    $stSql .= "     , sw_cgm.bairro                                                                                                    \n";
    $stSql .= "     , sw_municipio.nom_municipio                                                                                       \n";
    $stSql .= "     , sw_cgm.fone_comercial                                                                                            \n";
    $stSql .= "  FROM sw_cgm_pessoa_juridica                                                                                           \n";
    $stSql .= "     , sw_cgm                                                                                                           \n";
    $stSql .= "     , sw_municipio                                                                                                     \n";
    $stSql .= "     , estagio.instituicao_ensino                                                                                       \n";
    $stSql .= " WHERE sw_cgm_pessoa_juridica.numcgm = sw_cgm.numcgm                                                                    \n";
    $stSql .= "   AND sw_cgm.cod_uf = sw_municipio.cod_uf                                                                              \n";
    $stSql .= "   AND sw_cgm.cod_municipio = sw_municipio.cod_municipio                                                                \n";
    $stSql .= "   AND sw_cgm.numcgm = instituicao_ensino.numcgm                                                                  \n";

    return $stSql;
}

function recuperaGrausDeInstituicaoEnsino(&$rsRecordSet, $stFiltro = "", $stOrdem ="", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stOrdem = $stOrdem ? $stOrdem : " ORDER BY descricao ";
    $stSql  = $this->montaRecuperaGrausDeInstituicaoEnsino().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaGrausDeInstituicaoEnsino()
{
    $stSql .= "SELECT grau.*                                                   \n";
    $stSql .= "  FROM estagio.curso_instituicao_ensino                         \n";
    $stSql .= "     , estagio.curso                                            \n";
    $stSql .= "     , estagio.grau                                             \n";
    $stSql .= " WHERE curso_instituicao_ensino.cod_curso = curso.cod_curso     \n";
    $stSql .= "   AND curso.cod_grau = grau.cod_grau                           \n";
    $stSql .= ( $this->getDado("numcgm") != "" ) ? "   AND curso_instituicao_ensino.numcgm = ".$this->getDado("numcgm")."\n" : "";
    $stSql .= "GROUP BY grau.cod_grau,grau.descricao                           \n";

    return $stSql;
}

}
?>
