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
    * Classe de mapeamento da função ()
    * Data de Criação: 17/08/2011

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Davi Ritter Aroldi
    * @package URBEM
    * @subpackage Mapeamento

    * $Id:

* Casos de uso: uc-05.03.11
*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';

/**
  * Data de Criação: 17/08/2011

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Davi Ritter Aroldi

  * @package URBEM
  * @subpackage Mapeamento
*/
class FRecuperaAtributoCarneManaquiri extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function FRecuperaAtributoCarneManaquiri()
{
    parent::Persistente();
    $this->AddCampo('uso','char'  ,false       ,'150'     ,false   ,false );
    $this->AddCampo('conservacao','char'  ,false       ,'150'     ,false   ,false );
    $this->AddCampo('padrao','char'  ,false       ,'150'     ,false   ,false );
    $this->AddCampo('tipo','char'  ,false       ,'150'     ,false   ,false );
}

function executaFuncao(&$rsRecordset, $stParametros, $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;

    $stSql  = $this->montaExecutaFuncao($stParametros);
    $this->setDebug($stSql);
    $obErro = $obConexao->executaSQL( $rsRecordset, $stSql, $boTransacao );

return $obErro;
}

function montaExecutaFuncao($stParametros)
{
    $stSql  = " SELECT * FROM recuperaAtributoCarneManaquiri(".$stParametros.") as (uso VARCHAR, conservacao VARCHAR, padrao VARCHAR, tipo VARCHAR)\r\n";

return $stSql;
}

}
?>
