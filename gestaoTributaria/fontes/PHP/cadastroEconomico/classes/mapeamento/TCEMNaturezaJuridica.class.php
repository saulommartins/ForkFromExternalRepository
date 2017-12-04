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
  * Classe de mapeamento da tabela ECONOMICO.NATUREZA_JURIDICA
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMNaturezaJuridica.class.php 59612 2014-09-02 12:00:51Z gelson $

    * Casos de uso: uc-05.02.08

*/

/*
$Log$
Revision 1.6  2007/02/27 13:48:29  cassiano
Bug #8434#

Revision 1.5  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.NATUREZA_JURIDICA
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMNaturezaJuridica extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMNaturezaJuridica()
{
    parent::Persistente();
    $this->setTabela('economico.natureza_juridica');

    $this->setCampoCod('cod_natureza');
    $this->setComplementoChave('');

    $this->AddCampo('cod_natureza','integer',true,'',true,false);
    $this->AddCampo('nom_natureza','varchar',true,'200',false,false);

}

function montaRecuperaRelacionamento()
{
     $stSql .= "SELECT                                       						\n";
     $stSql .= "    N.nom_natureza	,                        						\n";
     $stSql .= "    substr(N.cod_natureza::varchar,0,length(N.cod_natureza::varchar))	|| '-'||       	\n";
     $stSql .= "    substr(N.cod_natureza::varchar,length(N.cod_natureza::varchar),1) as cod_natureza	\n";
     $stSql .= "FROM                                         			\n";
     $stSql .= "    economico.natureza_juridica N            			\n";
     $stSql .= "LEFT JOIN                                    			\n";
     $stSql .= "    economico.baixa_natureza_juridica BN     			\n";
     $stSql .= "ON                                           			\n";
     $stSql .= "    BN.cod_natureza = N.cod_natureza         			\n";
     $stSql .= "WHERE                                        			\n";
     $stSql .= "    BN.cod_natureza IS NULL                  			\n";

     return $stSql;
}

function recuperaNaturezaParaBaixa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    return $this->executaRecupera("montaRecuperaNaturezaParaBaixa",$rsRecordSet, $stFiltro, $stOrdem, $boTransacao);
}

function montaRecuperaNaturezaParaBaixa()
{
     $stSql .= " SELECT                                                                                                     \n";
     $stSql .= "    substr(n.cod_natureza::varchar,0,length(n.cod_natureza::varchar))	|| '-'||       	                                        \n";
     $stSql .= "    substr(n.cod_natureza::varchar,length(n.cod_natureza::varchar),1) as cod_natureza,                                        \n";
     $stSql .= "    n.nom_natureza                                                                                          \n";
     $stSql .= "FROM                                                                                                        \n";
     $stSql .= "    economico.natureza_juridica as n                                                                        \n";
     $stSql .= "WHERE                                                                                                       \n";
     $stSql .= "    NOT EXISTS( SELECT                                                                                      \n";
     $stSql .= "                    *                                                                                       \n";
     $stSql .= "                FROM                                                                                        \n";
     $stSql .= "                    economico.baixa_natureza_juridica                                                       \n";
     $stSql .= "                WHERE                                                                                       \n";
     $stSql .= "                    baixa_natureza_juridica.cod_natureza = n.cod_natureza )                                 \n";
     $stSql .= "    AND NOT EXISTS( SELECT                                                                                  \n";
     $stSql .= "                        *                                                                                   \n";
     $stSql .= "                    FROM                                                                                    \n";
     $stSql .= "                        economico.empresa_direito_natureza_juridica                                         \n";
     $stSql .= "                    WHERE                                                                                   \n";
     $stSql .= "                        empresa_direito_natureza_juridica.cod_natureza = n.cod_natureza )                   \n";

     return $stSql;
}

}
