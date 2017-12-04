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
  * Página de
  * Data de criação : 25/10/2005

    * @author Analista:
    * @author Programador: Fernando Zank Correa Evangelista

    $Revision: 28706 $
    $Name$
    $Author: luiz $
    $Date: 2008-03-24 16:17:06 -0300 (Seg, 24 Mar 2008) $

    Caso de uso: uc-03.01.09
**/

/*
$Log$
Revision 1.8  2006/07/06 14:07:04  diego
Retirada tag de log com erro.

Revision 1.7  2006/07/06 12:11:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TPatrimonioNatureza extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TPatrimonioNatureza()
{
    parent::Persistente();
    
    $this->setTabela('patrimonio.natureza');
    
    $this->setCampoCod('cod_natureza');
    $this->setComplementoChave('');
    
    $this->AddCampo('cod_natureza'  ,'integer'  ,true   ,''    ,true    ,false);
    $this->AddCampo('cod_tipo'      ,'integer'  ,true   ,'60'  ,false   ,true);
    $this->AddCampo('nom_natureza'  ,'varchar'  ,true   ,'60'  ,false   ,false);

}

function recuperaNatureza(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaNatureza().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaNatureza()
{
    $stSql  = "select                       \n";
    $stSql .= "    *                        \n";
    $stSql .= "from                         \n";
    $stSql .= "    patrimonio.natureza  \n";

    return $stSql;
}


function recuperaListaNatureza(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaListaNatureza().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaListaNatureza()
{
    $stSql  = "SELECT natureza.cod_natureza
                    , natureza.nom_natureza
                    , natureza.cod_tipo
                    , tipo_natureza.descricao
                 FROM patrimonio.natureza 
       
           INNER JOIN patrimonio.tipo_natureza
                   ON natureza.cod_tipo = tipo_natureza.codigo \n";

    return $stSql;
}

function recuperaMaxNatureza(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;
    $stSql = $this->montaRecuperaMaxNatureza().$stFiltro.$stOrdem;
    $this->stDebug = $stSql;
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMaxNatureza()
{
    $stSql  = "select                       \n";
    $stSql .= "    max(cod_natureza) as max                        \n";
    $stSql .= "from                         \n";
    $stSql .= "    patrimonio.natureza  \n";

    return $stSql;

}

}

?>
