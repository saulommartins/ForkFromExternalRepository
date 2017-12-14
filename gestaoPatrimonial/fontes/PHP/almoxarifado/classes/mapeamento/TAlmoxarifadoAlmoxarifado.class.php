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
    * Classe de mapeamento da tabela ALMOXARIFADO.ALMOXARIFADO
    * Data de Criação: 26/10/2005

    * @author Analista: Diego Victoria
    * @author Desenvolvedor: Fernando Zank Correa Evangelista

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.03.01
                    uc-03.03.14
*/

/*
$Log$
Revision 1.10  2006/07/06 14:04:43  diego
Retirada tag de log com erro.

Revision 1.9  2006/07/06 12:09:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  ALMOXARIFADO.ALMOXARIFADO
  * Data de Criação: 26/10/2005

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Fernando Zank Correa Evangelista

  * @package URBEM
  * @subpackage Mapeamento
*/

class TAlmoxarifadoAlmoxarifado extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */

    public function TAlmoxarifadoAlmoxarifado()
    {
        parent::Persistente();
        $this->setTabela('almoxarifado.almoxarifado');

        $this->setCampoCod('cod_almoxarifado');
        $this->setComplementoChave('');

        $this->AddCampo('cod_almoxarifado','integer',true,'',true,false);
        $this->AddCampo('cgm_responsavel','integer',true,'',false,true);
        $this->AddCampo('cgm_almoxarifado','integer',true,'',false,true);
    }

    public function montaRecuperaRelacionamento()
    {
        $stSql =  "select                                    \n";
        $stSql .= "       a.cod_almoxarifado as codigo,      \n";
        $stSql .= "       a.cgm_responsavel  as cgm_r,       \n";
        $stSql .= "       a.cgm_almoxarifado as cgm_a,       \n";
        $stSql .= "       sr.nom_cgm         as nom_r,       \n";
        $stSql .= "       sa.nom_cgm         as nom_a        \n";
        $stSql .= "from                                      \n";
        $stSql .= "       almoxarifado.almoxarifado a,       \n";
        $stSql .= "       sw_cgm sr,                         \n";
        $stSql .= "       sw_cgm sa                          \n";
        $stSql .= "where                                     \n";
        $stSql .= "        a.cgm_responsavel = sr.numcgm and \n";
        $stSql .= "        a.cgm_almoxarifado = sa.numcgm";

        return $stSql;
    }

    public function recuperaAlmoxarifados(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaAlmoxarifados().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, "", $boTransacao );
    }

    public function montaRecuperaAlmoxarifados()
    {
        $stSql  = "SELECT almoxarifado.cod_almoxarifado                 \n";
        $stSql .= "      ,cgm.numcgm                                    \n";
        $stSql .= "      ,cgm.nom_cgm                                   \n";
        $stSql .= "FROM   sw_cgm as cgm                                 \n";
        $stSql .= "      ,almoxarifado.almoxarifado as almoxarifado     \n";
        $stSql .= "WHERE  cgm.numcgm = almoxarifado.cgm_almoxarifado    \n";

        return $stSql;
    }
}
