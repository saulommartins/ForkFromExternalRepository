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
    * Classe de mapeamento da tabela compras.compra_direta
    * Data de Criação: 30/01/2007

    * @author Analista: Gelson
    * @author Desenvolvedor: Henrique Boaventura

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 56934 $
    $Name$
    $Author: gelson $
    $Date: 2014-01-08 17:46:44 -0200 (Wed, 08 Jan 2014) $

    * Casos de uso: uc-06.04.00
*/

/*
$Log$
Revision 1.4  2007/06/12 20:44:11  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.3  2007/06/12 18:34:05  hboaventura
inclusão dos casos de uso uc-06.04.00

Revision 1.2  2007/05/24 13:18:37  hboaventura
Arquivos para geração do TCMGO

Revision 1.1  2007/05/18 14:49:24  hboaventura
Arquivos para geração do TCMGO

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTCEMGBalanceteExtmmaa extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/
    public function TTCEMGBalanceteExtmmaa()
    {
        parent::Persistente();
        $this->setTabela("tcemg.balancete_extmmaa");

        $this->setCampoCod('cod_plano');
        $this->setComplementoChave('exercicio');

        $this->AddCampo( 'cod_plano' ,'integer' ,true, ''   ,true ,true  );
        $this->AddCampo( 'exercicio' ,'char' ,true, '4'   ,true ,true  );
        $this->AddCampo( 'categoria' ,'integer' ,true, '' ,false ,false );
        $this->AddCampo( 'tipo_lancamento' ,'integer' ,true, '' ,false ,false );
        $this->AddCampo( 'sub_tipo_lancamento' ,'integer' ,true, '' ,false ,false );
    }

    public function recuperaRelacionamento(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaRelacionamento",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaRelacionamento()
    {
        $stSql = "
            SELECT  balancete_extmmaa.cod_plano
                 ,  tipo_lancamento
                 ,  cod_estrutural
                 ,  nom_conta
              FROM  tcemg.balancete_extmmaa
        INNER JOIN  contabilidade.plano_analitica
                ON  plano_analitica.cod_plano = balancete_extmmaa.cod_plano
               AND  plano_analitica.exercicio = balancete_extmmaa.exercicio
        INNER JOIN  contabilidade.plano_conta
                ON  plano_conta.cod_conta = plano_analitica.cod_conta
               AND  plano_conta.exercicio = plano_analitica.exercicio
             WHERE  balancete_extmmaa.exercicio = '".$this->getDado('exercicio')."' ";
        if ( $this->getDado('tipo_lancamento') ) {
            $stSql.= " AND balancete_extmmaa.tipo_lancamento = ".$this->getDado('tipo_lancamento')." ";
        }
        if ( $this->getDado('sub_tipo_lancamento') ) {
            if ($this->getDado('sub_tipo_lancamento') > 4) {
                $stSql.= " AND  balancete_extmmaa.sub_tipo_lancamento > 4 ";
            } else {
                $stSql.= " AND  balancete_extmmaa.sub_tipo_lancamento = ".$this->getDado('sub_tipo_lancamento')." ";
            }
        }
        if ( $this->getDado('categoria') ) {
            $stSql.= " AND  balancete_extmmaa.categoria = ".$this->getDado('categoria')." ";
        }
        if ( $this->getDado('cod_plano') ) {
            $stSql.= " AND  balancete_extmmaa.cod_plano = ".$this->getDado('cod_plano')." ";
        }

        return $stSql;
    }

    public function recuperaUltimoSequencialSubTipo(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaRecuperaUltimoSequencialSubTipo",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
    }
    public function montaRecuperaUltimoSequencialSubTipo()
    {
        $stSql = "
            SELECT  max(sub_tipo_lancamento) as max_sub_tipo
              FROM  tcemg.balancete_extmmaa
        WHERE  balancete_extmmaa.exercicio = '".$this->getDado('exercicio')."' ";
        if ( $this->getDado('tipo_lancamento') ) {
            $stSql.= " AND balancete_extmmaa.tipo_lancamento = ".$this->getDado('tipo_lancamento')." ";
        }
        if ( $this->getDado('sub_tipo_lancamento') ) {
            $stSql.= " AND  balancete_extmmaa.sub_tipo_lancamento = ".$this->getDado('sub_tipo_lancamento')." ";
        }
        if ( $this->getDado('categoria') ) {
            $stSql.= " AND  balancete_extmmaa.categoria = ".$this->getDado('categoria')." ";
        }
        if ( $this->getDado('cod_plano') ) {
            $stSql.= " AND  balancete_extmmaa.cod_plano = ".$this->getDado('cod_plano')." ";
        }

        return $stSql;
    }
    
    public function __destruct(){}

}

?>