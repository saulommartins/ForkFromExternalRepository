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
  * Classe de mapeamento da tabela ECONOMICO.EMPRESA_DIREITO_NATUREZA_JURIDICA
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMEmpresaDireitoNaturezaJuridica.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.08
*/

/*
$Log$
Revision 1.6  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.EMPRESA_DIREITO_NATUREZA_JURIDICA
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMEmpresaDireitoNaturezaJuridica extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMEmpresaDireitoNaturezaJuridica()
{
    parent::Persistente();
    $this->setTabela('economico.empresa_direito_natureza_juridica');

    $this->setCampoCod('');
    $this->setComplementoChave('inscricao_economica,timestamp');

    $this->AddCampo('inscricao_economica','integer',true,'',true,true);
    $this->AddCampo('timestamp','timestamp',false,'',true,false);
    $this->AddCampo('cod_natureza','integer',true,'',false,true);

}

function recuperaEmpresaDireitoNatureza(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaEmpresaDireitoNatureza().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );

    return $obErro;
}

function montaRecuperaEmpresaDireitoNatureza()
{
    $stSql .= "SELECT                                                                       \n";
//  $stSql .= "    nj.cod_natureza,                                                         \n";
    $stSql .= "    substr(nj.cod_natureza::varchar,0,length(nj.cod_natureza::varchar))    || '-'||            \n";
    $stSql .= "    substr(nj.cod_natureza::varchar,length(nj.cod_natureza::varchar),1) as cod_natureza,       \n";
    $stSql .= "    nj.nom_natureza                                                          \n";
    $stSql .= "FROM                                                                         \n";
    $stSql .= "    economico.empresa_direito_natureza_juridica as enj,                        \n";
    $stSql .= "    economico.natureza_juridica as nj                                          \n";
    $stSql .= "WHERE                                                                        \n";
    $stSql .= "    enj.cod_natureza = nj.cod_natureza                                       \n";

    return $stSql;
}

}
