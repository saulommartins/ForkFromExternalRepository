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
  * Classe de mapeamento da tabela ECONOMICO.TIPO_LICENCA_DIVERSA
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento

    * $Id: TCEMTipoLicencaDiversa.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.02.11
*/

/*
$Log$
Revision 1.6  2007/05/14 20:39:26  dibueno
Alterações para possibilitar a emissao do alvará diverso

Revision 1.5  2006/09/15 12:08:26  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

/**
  * Efetua conexão com a tabela  ECONOMICO.TIPO_LICENCA_DIVERSA
  * Data de Criação: 17/11/2004

  * @author Analista: Ricardo Lopes de Alencar
  * @author Desenvolvedor: Tonismar Régis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class TCEMTipoLicencaDiversa extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TCEMTipoLicencaDiversa()
{
    parent::Persistente();
    $this->setTabela('economico.tipo_licenca_diversa');

    $this->setCampoCod('cod_tipo');
    $this->setComplementoChave('');

    $this->AddCampo('cod_tipo','integer',true,'',true,false);
    $this->AddCampo('nom_tipo','varchar',true,'80',false,false);
    $this->AddCampo('cod_utilizacao', 'integer', false, '', false, true);

}
function recuperaTipoLicencaDiversa(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaTipoLicencaDiversa().$stFiltro;
    $stSql .= "  GROUP BY                                                                        \n";
    $stSql .="      etld.cod_tipo, etld.nom_tipo, etlmd.cod_documento, etlmd.cod_tipo_documento, etld.cod_utilizacao \n";
    $stSql .= $stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaTipoLicencaDiversa()
{
    $stSQL  ="  SELECT                                                                          \n";
    $stSQL .="      etld.cod_tipo                                                               \n";
    $stSQL .="      , etld.nom_tipo                                                             \n";
    $stSQL .="      , max(timestamp) as timestamp                                               \n";
    $stSQL .="      , etlmd.cod_documento                                                       \n";
    $stSQL .="      , etlmd.cod_tipo_documento                                                  \n";
    $stSQL .="      , etld.cod_utilizacao                                                      \n";
    $stSQL .="  FROM                                                                            \n";
    $stSQL .="      economico.tipo_licenca_diversa as etld                                      \n";
    $stSQL .="        LEFT JOIN economico.tipo_licenca_modelo_documento as etlmd                \n";
    $stSQL .="        ON etlmd.cod_tipo = etld.cod_tipo                                         \n";

    return $stSQL;

}

}
