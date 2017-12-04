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
    * Data de Criação: 09/10/2007

    * @author Analista: Gelson W. Gonçalves
    * @author Desenvolvedor: Henrique Girardi dos Santos

    * @package URBEM
    * @subpackage

    $Revision: 26063 $
    $Name$
    $Author: girardi $
    $Date: 2007-10-11 18:31:04 -0300 (Qui, 11 Out 2007) $

    * Casos de uso : uc-03.05.22
*/

/*
$Log$
Revision 1.1  2007/10/11 21:30:32  girardi
adicionando ao repositório (rescisão de contrato e aditivos de contrato)

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TLicitacaoRescisaoContratoResponsavelJuridico extends Persistente
{

    /**
    * Método Construtor
    * @access Private
    */
    public function TLicitacaoRescisaoContratoResponsavelJuridico()
    {
        parent::Persistente();
        $this->setTabela("licitacao.rescisao_contrato_responsavel_juridico");

        $this->setCampoCod('num_contrato');
        $this->setComplementoChave('exercicio_contrato, cod_entidade');

        $this->AddCampo('exercicio_contrato', 'character',true, '4', true, true);
        $this->AddCampo('cod_entidade', 'integer', true, '', true, true);
        $this->AddCampo('num_contrato', 'integer', true, '', true, true);
        $this->AddCampo('numcgm', 'integer', true, '', false, true);
    }
    
    function recuperaDadosCGM(&$rsRecordSet, $stFiltro = "", $stOrdem = "", $boTransacao = "") 
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $stSql = $this->montaRecuperaDadosCGM().$stFiltro.$stOrdem;
        $this->stDebug = $stSql;
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );
    
        return $obErro;
    }
    
    function montaRecuperaDadosCGM()
    {
        $stSql = "SELECT sw_cgm.nom_cgm,
                         licitacao.rescisao_contrato_responsavel_juridico.*
                    FROM licitacao.rescisao_contrato_responsavel_juridico
              INNER JOIN sw_cgm
                      ON sw_cgm.numcgm = rescisao_contrato_responsavel_juridico.numcgm
                   WHERE rescisao_contrato_responsavel_juridico.num_contrato = ".$this->getDado("num_contrato")."
                     AND rescisao_contrato_responsavel_juridico.exercicio_contrato = '".$this->getDado("exercicio_contrato")."'
                     AND rescisao_contrato_responsavel_juridico.cod_entidade = ".$this->getDado("cod_entidade")." 
                ";
                      
        return $stSql;
        
    }
}
