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
    * @author Desenvolvedor: Lucas Teixeira Stephanou

    * @package URBEM
    * @subpackage Mapeamento

    $Revision: 19911 $
    $Name$
    $Author: domluc $
    $Date: 2007-02-06 16:22:07 -0200 (Ter, 06 Fev 2007) $

    * Casos de uso: uc-03.04.33
                    uc-03.04.32
*/

/*
$Log$
Revision 1.1  2007/02/06 18:21:17  domluc
CompraDireta

*/

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

/**
  * Efetua conexão com a tabela  compras.mapa
  * Data de Criação: 30/06/2006

  * @author Analista: Diego Victoria
  * @author Desenvolvedor: Leandro André Zis

  * @package URBEM
  * @subpackage Mapeamento
*/
class TComprasCompraDiretaAnulacao extends Persistente
{
    /**
    * Método Construtor
    * @access Private
*/
    public function TComprasCompraDiretaAnulacao()
    {
        parent::Persistente();
        $this->setTabela("compras.compra_direta_anulacao");

        $this->setCampoCod('');
        $this->setComplementoChave('cod_compra_direta,cod_entidade,exercicio_entidade,cod_modalidade');

        $this->AddCampo( 'cod_compra_direta'	,'integer' ,true	, ''	,true	,true  );
        $this->AddCampo( 'cod_entidade'       	,'integer'	,true	, '' 	,true  	,true	);
        $this->AddCampo( 'exercicio_entidade'   ,'char'		,true	, '4' 	,true  	,true	);
        $this->AddCampo( 'cod_modalidade'      	,'integer'	,true	, '' 	,true  	,true	);
        $this->AddCampo( 'motivo'  				,'varchar'	,true		, '200'	,false 	,false  );
        $this->AddCampo( 'timestamp'          	,'timestamo',false 	, '' 	,false ,false   );
    }
}
