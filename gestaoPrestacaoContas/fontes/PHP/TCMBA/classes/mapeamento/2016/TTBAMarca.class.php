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
    * Data de Criação: 20/08/2008

    * @author Analista      : Tonismar Régis Bernardo
    * @author Desenvolvedor : Henrique Boaventura

    * @ignore

    * $Id: TTBAMarca.class.php 62823 2015-06-24 17:22:01Z evandro $

    * Casos de uso: uc-06.05.00
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  *
  * Data de Criação: 30/08/2007

  * @author Analista: Diego Barbosa Victoria
  * @author Desenvolvedor: Diego Barbosa Victoria

*/
class TTBAMarca extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TTBAMarca()
{
    parent::Persistente();
    $this->setTabela('tcmba.marca');

    $this->setCampoCod('cod_marca_tcm');
    $this->setComplementoChave('cod_tipo_tcm');

    $this->AddCampo('cod_marca_tcm','integer',true,'',true,false);
    $this->AddCampo('cod_tipo_tcm' ,'integer',false,'',true,'TTBATipoVeiculo');
    $this->AddCampo('descricao','varchar',false,'200',false,false);
    $this->AddCampo('cod_marca','integer',false,'',false,'TFrotaMarca');
}

function recuperaMarca(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
{
    return $this->executaRecupera("montaRecuperaMarca",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

function montaRecuperaMarca()
{
    $stSql = "
        SELECT tcmba_marca.cod_marca_tcm
             , tcmba_marca.cod_tipo_tcm
             , tcmba_marca.descricao
             , tcmba_marca.cod_marca
             , tipo_veiculo.descricao AS nom_tipo_veiculo
          FROM tcmba.marca AS tcmba_marca
    INNER JOIN tcmba.tipo_veiculo
            ON tipo_veiculo.cod_tipo_tcm = tcmba_marca.cod_tipo_tcm
    ";
    if ( $this->getDado('cod_tipo_tcm') != '' ) {
        $stSql .= " WHERE tcmba_marca.cod_tipo_tcm = ".$this->getDado('cod_tipo_tcm')." ";
    }
    $stSql .= "
      ORDER BY tcmba_marca.descricao
    ";

    return $stSql;
}

}
