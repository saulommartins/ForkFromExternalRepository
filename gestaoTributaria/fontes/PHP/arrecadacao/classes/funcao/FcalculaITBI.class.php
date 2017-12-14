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
    * Classe de mapeamento da função calculo itbi;
    * Data de CriaÃ§Ã£o: 24/01/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Lucas Teixeira Stephanou
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: FcalculaITBI.class.php 65763 2016-06-16 17:31:43Z evandro $

* Casos de uso: uc-05.03.11
*/

/*
$Log$
Revision 1.5  2006/09/15 11:49:45  fabio
corrigidas tags de caso de uso

Revision 1.4  2006/09/15 10:28:15  fabio
correção do cabeçalho,
adicionado trecho de log do CVS

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkDB.inc.php';

/**
  * Data de CriaÃ§Ã£o: 20/10/2005

  * @author Analista: Fabio Bertoldi Rodrigues
  * @author Desenvolvedor: Tonismar RÃ©gis Bernardo

  * @package URBEM
  * @subpackage Mapeamento
*/
class FcalculaITBI extends Persistente
{
/**
    * MÃ©todo Construtor
    * @access Private
*/
function FcalculaITBI()
{
    parent::Persistente();
    $this->AddCampo('valor','numeric'  ,false       ,''     ,false   ,false );
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
    $stSql  = " SELECT calculaITBI(".$stParametros.") as valor \r\n";
    //$stSql  = " SELECT itbi2006(".$stParametros.") as valor \r\n";

return $stSql;
}

}
?>
