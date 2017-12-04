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
  * Classe de mapeamento da tabela ECONOMICO.ELEMENTO_ATIV_CAD_ECONOMICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMElementoAtivCadEconomico.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.05
*/

/*
$Log$
Revision 1.9  2006/11/17 12:43:15  domluc
Correção Bug #7437#

Revision 1.8  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.ELEMENTO_ATIV_CAD_ECONOMICO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMElementoAtivCadEconomico extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMElementoAtivCadEconomico()
{
    parent::Persistente();
    $this->setTabela('economico.elemento_ativ_cad_economico');

    $this->setCampoCod('');
    $this->setComplementoChave('inscricao_economica,cod_atividade,ocorrencia_atividade,cod_elemento,ocorrencia_elemento,ativo');

    $this->AddCampo('inscricao_economica','integer',true,'',true,true);
    $this->AddCampo('cod_atividade','integer',true,'',true,true);
    $this->AddCampo('ocorrencia_atividade','integer',true,'',true,true);
    $this->AddCampo('cod_elemento','integer',true,'',true,true);
    $this->AddCampo('ocorrencia_elemento','integer',true,'',true,true);
    $this->AddCampo('ativo','boolean',false,'',false,false);
}

function montaRecuperaRelacionamento()
{
       $stSql .= "SELECT ace.*                                                                                                  \n";
       $stSql .= "     , a.cod_estrutural                                                                                       \n";
       $stSql .= "     , e.nom_elemento                                                                                         \n";
       $stSql .= "     , economico.fn_busca_atributos_elementos ( ace.inscricao_economica , ace.cod_atividade,ace.cod_elemento
                                                       , ace.ocorrencia_elemento,ace.ocorrencia_atividade ) as atributos        \n";
       $stSql .= "  FROM economico.elemento_ativ_cad_economico ace                                                              \n";
       $stSql .= "       INNER JOIN economico.atividade a                                                                       \n";
       $stSql .= "               ON a.cod_atividade = ace.cod_atividade                                                         \n";
       $stSql .= "       INNER JOIN economico.elemento_atividade ae                                                             \n";
       $stSql .= "               ON ace.cod_elemento = ae.cod_elemento                                                          \n";
       $stSql .= "              AND ace.cod_atividade= ae.cod_atividade                                                         \n";
       $stSql .= "       INNER JOIN economico.elemento e                                                                        \n";
       $stSql .= "               ON ae.cod_elemento = e.cod_elemento
                    WHERE ace.ocorrencia_atividade = (
                            SELECT
                                MAX( atividade_cadastro_economico.ocorrencia_atividade )
                            FROM
                                economico.atividade_cadastro_economico
                            WHERE
                                atividade_cadastro_economico.inscricao_economica = ace.inscricao_economica
                    ) \n";

    return $stSql;

}

function recuperaMaxOcorrenciaElemento(&$rsRecordSet, $stFiltro="", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaMaxOcorrenciaElemento().$stFiltro.$stOrdem;

    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}
function montaRecuperaMaxOcorrenciaElemento()
{
    $stSql  = " SELECT max(ocorrencia_elemento) as max_ocorrencia           \n";
    $stSql .= " FROM                                                        \n";
    $stSql .= "     economico.elemento_ativ_cad_economico ace               \n";

    return $stSql;

}

function recuperaElementoInscricao(&$rsRecordSet, $stFiltro, $stOrdem, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaElementoInscricao().$stFiltro.$stOrdem;

    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}
function montaRecuperaElementoInscricao()
{
    $stSql  = " SELECT ace.cod_elemento, ace.inscricao_economica           \n";
    $stSql .= " FROM                                                        \n";
    $stSql .= "     economico.elemento_ativ_cad_economico as ace               \n";

    return $stSql;

}

}
