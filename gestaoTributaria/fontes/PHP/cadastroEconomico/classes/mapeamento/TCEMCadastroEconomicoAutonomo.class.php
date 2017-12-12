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
  * Classe de mapeamento da tabela ECONOMICO.CADASTRO_ECONOMICO_AUTONOMO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMCadastroEconomicoAutonomo.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.10
*/

/*
$Log$
Revision 1.7  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.CADASTRO_ECONOMICO_AUTONOMO
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMCadastroEconomicoAutonomo extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMCadastroEconomicoAutonomo()
{
    parent::Persistente();
    $this->setTabela('economico.cadastro_economico_autonomo');

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
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaInscricao()
{
    $stSql .= "SELECT                                                   \n";
    $stSql .= "    ce.inscricao_economica,                              \n";
    $stSql .= "    TO_CHAR(ce.dt_abertura,'dd/mm/yyyy') as dt_abertura, \n";
    $stSql .= "    au.numcgm,                                           \n";
    $stSql .= "    cgm.nom_cgm                                          \n";
    $stSql .= "FROM                                                     \n";
    $stSql .= "    economico.cadastro_economico as ce,                  \n";
    $stSql .= "    economico.cadastro_economico_autonomo as au,         \n";
    $stSql .= "    sw_cgm as cgm                                        \n";
    $stSql .= "WHERE                                                    \n";
    $stSql .= "ce.inscricao_economica = au.inscricao_economica   and    \n";
    $stSql .= "cgm.numcgm = au.numcgm                                   \n";

    return $stSql;
}

}
