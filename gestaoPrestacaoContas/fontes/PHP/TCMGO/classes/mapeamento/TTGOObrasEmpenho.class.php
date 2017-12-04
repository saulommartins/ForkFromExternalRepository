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

    * @package URBEM
    * @subpackage Mapeamento

    * Casos de uso: uc-06.04.00
*/

/*
$Log$
Revision 1.1  2007/10/10 15:39:29  bruce
*** empty log message ***

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TTGOObrasEmpenho extends Persistente
{
    public function TTGOObrasEmpenho()
    {
        parent::Persistente();
        $this->setTabela("tcmgo.obra_empenho");

        $this->setCampoCod('cod_obra');
        $this->setComplementoChave('ano_obra,cod_empenho, cod_entidade, exercicio');

        $this->AddCampo( 'cod_obra'      ,'integer' ,true, ''  ,true ,true  );
        $this->AddCampo( 'ano_obra'      ,'integer' ,true, '' ,true ,false );
        $this->AddCampo( 'cod_empenho'   ,'integer' ,true, '' ,true ,false  );
        $this->AddCampo( 'cod_entidade'  ,'integer' ,true, '' ,true ,false  );
        $this->AddCampo( 'exercicio'     ,'varchar' ,true, '4' ,true ,false );
    }

    public function buscaEmpenhos(&$rsRecordSet,$stFiltro="",$stOrder="",$boTransacao="")
    {
        return $this->executaRecupera("montaBuscaEmpenhos",$rsRecordSet,$stFiltro,$stOrder,$boTransacao);
}

    public function montaBuscaEmpenhos()
    {
        $stSql ="select obra_empenho.*
                      , sw_cgm.nom_cgm
                      , to_char(empenho.dt_empenho, 'dd/mm/yyyy') as dt_empenho
                   from tcmgo.obra_empenho
                   join empenho.empenho
                     on ( empenho.exercicio    = obra_empenho.exercicio
                    and   empenho.cod_entidade = obra_empenho.cod_entidade
                    and   empenho.cod_empenho  = obra_empenho.cod_empenho )
                   join empenho.pre_empenho
                     on ( empenho.exercicio       = pre_empenho.exercicio
                    and   empenho.cod_pre_empenho = pre_empenho.cod_pre_empenho )
                   join sw_cgm
                     on ( pre_empenho.cgm_beneficiario = sw_cgm.numcgm )
                 where obra_empenho.ano_obra = " . $this->getDado( 'ano_obra' ) .
                  "and obra_empenho.cod_obra = " . $this->getDado( 'cod_obra' ) ;

        return $stSql;
    }

}
