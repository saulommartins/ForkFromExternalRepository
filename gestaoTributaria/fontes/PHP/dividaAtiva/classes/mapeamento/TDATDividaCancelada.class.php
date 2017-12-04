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
    * Classe de mapeamento da tabela DIVIDA.DIVIDA_CANCELADA
    * Data de Criação: 06/10/2006

    * @author Analista: Fabio Bertoldi Rodrigues
    * @author Desenvolvedor: Diego Bueno Coelho
    * @package URBEM
    * @subpackage Mapeamento

    * $Id: TDATDividaCancelada.class.php 59612 2014-09-02 12:00:51Z gelson $

* Casos de uso: uc-05.04.02
*/

/*
$Log$
Revision 1.3  2007/08/02 19:36:48  cercato
adicionando campo timestamp.

Revision 1.2  2007/07/17 14:37:11  cercato
correcao para rotina de cancelamento de divida.

Revision 1.1  2006/10/06 17:00:08  dibueno
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

class TDATDividaCancelada extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function TDATDividaCancelada()
    {
        parent::Persistente();
        $this->setTabela('divida.divida_cancelada');

        $this->setCampoCod('');
        $this->setComplementoChave('exercicio, cod_inscricao, num_parcelamento');

        $this->AddCampo('exercicio','varchar',true,'4',true,true);
        $this->AddCampo('cod_inscricao','integer',true,'',true,true);
        $this->AddCampo('numcgm','integer',true,'',false,true);
        $this->AddCampo('motivo','varchar',true,'80',false,false);
        $this->AddCampo('timestamp','timestamp',false,'',false,false);
    }

    public function consultarMascaraProcesso(&$stMascaraProcesso, $inExercicio , $boTransacao = "")
    {
        include_once( CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php" );

        $stFiltro  = " WHERE COD_MODULO = 5 AND parametro = 'mascara_processo' ";
        $stFiltro .= " AND exercicio = '".$inExercicio."' ";
        $stOrdem   = " ORDER BY EXERCICIO DESC ";

        $obTConfiguracao  = new TAdministracaoConfiguracao;
        $obErro = $obTConfiguracao->recuperaTodos( $rsRecordSet, $stFiltro, $stOrdem, $boTransacao );
        if ( !$obErro->ocorreu() and !$rsRecordSet->eof() ) {
            $stMascaraProcesso = $rsRecordSet->getCampo( "valor" );
        }

        return $obErro;
    }

    public function recuperaDividaCancelada(&$rsRecordSet, $stCondicao="" , $stOrdem = "" , $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;

        $stSql = $this->montaRecuperaDividaCancelada($stCondicao).$stCondicao.$stOrdem;

        $this->setDebug( $stSql );
        $obErro = $obConexao->executaSQL( $rsRecordSet, $stSql, $boTransacao );

        return $obErro;
    }

    public function montaRecuperaDividaCancelada($stCondicao='')
    {
        $stSql = "SELECT *
                    FROM divida.divida_cancelada
                   WHERE exercicio     = '".$this->getDado('exercicio')."'
                     AND cod_inscricao = ".$this->getDado('cod_inscricao');

        return $stSql;
    }

}// end of class

?>
