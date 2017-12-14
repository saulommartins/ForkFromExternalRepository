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
    * Classe de mapeamento da tabela ALMOXARIFADO.CATALOGO_NIVEIS
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.03.04 uc-03.03.05
*/

/*
$Log$
Revision 1.9  2006/07/06 14:04:43  diego
Retirada tag de log com erro.

Revision 1.8  2006/07/06 12:09:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.CATALOGO_NIVEIS
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/
class TAlmoxarifadoCatalogoNiveis extends Persistente
{
/**
    * Método Construtor
    * @access Private
*/
function TAlmoxarifadoCatalogoNiveis()
{
    parent::Persistente();
    $this->setTabela('almoxarifado.catalogo_niveis');

    $this->setCampoCod('nivel');
    $this->setComplementoChave('cod_catalogo');

    $this->AddCampo('nivel','integer',true,'',true,false);
    $this->AddCampo('cod_catalogo','integer',true,'',true,true);
    $this->AddCampo('mascara','varchar',true,'10',false,false);
    $this->AddCampo('descricao','varchar',true,'160',false,false);

}

function montaRecuperaRelacionamento()
{
    $stSql =  "select                              \n";
    $stSql .=  "      nivel,                       \n";
    $stSql .= "       mascara,                     \n";
    $stSql .= "       descricao                    \n";
    $stSql .= "from                                \n";
    $stSql .= "       almoxarifado.catalogo_niveis   ";

    return $stSql;
}

function recuperaMascaraCompleta(&$rsRecordSet, $stFiltro = "", $boTransacao = "")
{
    $obErro      = new Erro;
    $obConexao   = new Conexao;
    $rsRecordSet = new RecordSet;

    $stSql = $this->montaRecuperaMascaraCompleta().$stFiltro;
    $this->setDebug( $stSql );
    $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

    return $obErro;
}

function montaRecuperaMascaraCompleta()
{
    $stSql .= "select                                       \n";
    $stSql .= "        publico.concatenar_ponto(mascara) as mascara    \n";
    $stSql .= "from (                                       \n";
    $stSql .= "        select                               \n";
    $stSql .= "                mascara                      \n";
    $stSql .= "        from                                 \n";
    $stSql .= "                almoxarifado.catalogo_niveis \n";

    if ($this->getDado("codCatalogo")) {
        $stSql .= "where                                                                                                    \n";
        $stSql .= "cod_catalogo = " . $this->getDado("codCatalogo") . "\n";
    }

    $stSql .= "        order by nivel                       \n";
    $stSql .= "     )                                       \n";
    $stSql .= "     as cn                                   \n";

    return $stSql;
}

}
