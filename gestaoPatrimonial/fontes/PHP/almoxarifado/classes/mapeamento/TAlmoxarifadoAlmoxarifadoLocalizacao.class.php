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
    * Classe de mapeamento da tabela ALMOXARIFADO.ALMOXARIFADO_LOCALIZACAO
    * Data de Criação: 16/02/2006

    * @author Analista      : Diego Victoria
    * @author Desenvolvedor : Rodrigo

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 12234 $
    $Name$
    $Author: diego $
    $Date: 2006-07-06 11:08:37 -0300 (Qui, 06 Jul 2006) $

    * Casos de uso: uc-03.03.14
*/

/*
$Log$
Revision 1.6  2006/07/06 14:04:43  diego
Retirada tag de log com erro.

Revision 1.5  2006/07/06 12:09:27  diego

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TAlmoxarifadoAlmoxarifadoLocalizacao extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */

    public function TAlmoxarifadoAlmoxarifadoLocalizacao()
    {
        parent::Persistente();
        $this->setTabela('almoxarifado.localizacao');
        $this->setCampoCod('cod_almoxarifado');
        $this->setComplementoChave('');
        $this->AddCampo('cod_almoxarifado','integer',true,'',true,false);
        $this->AddCampo('mascara','varchar',true,'30','false',false);
    }

    public function montaComboAlmoxarifado()
    {
     $stSql = "Select cod_almoxarifado  ,       \n";
     $stSql.= "       cgm_responsavel   ,       \n";
     $stSql.= "       permite_manutencao        \n";
     $stSql.= "  From almoxarifado.almoxarifado \n";

    return $stSql;
   }

   public function recuperaLocalizacao(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "")
   {
       $obErro      = new Erro;
       $obConexao   = new Conexao;
       $rsRecordSet = new RecordSet;
       $stSql       = $this->montaRecuperaRelacionamento().$stFiltro.$stOrdem;
       $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

       return $obErro;
   }

   public function montaRecuperaRelacionamento()
   {
     $stSql = "Select localizacao.cod_almoxarifado      \n";
     $stSql.= "  From almoxarifado.localizacao       ,  \n";
     $stSql.= "       almoxarifado.localizacao_fisica   \n";
     $stSql.= " Where localizacao.cod_almoxarifado = localizacao_fisica.cod_almoxarifado \n";

    return $stSql;
   }
}
