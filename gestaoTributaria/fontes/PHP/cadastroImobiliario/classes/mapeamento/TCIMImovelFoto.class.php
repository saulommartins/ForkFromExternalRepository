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
 * Classe mapeamento para a tabela imovel_foto
 *
 * @category   Urbem
 * @package    Framework
 * @author     Analista Cassiano de Vasconcellos Ferreira <cassiano.ferreira@cnm.org.br>
 * @author     Desenvolvedor Cassiano de Vasconcellos Ferreira <cassiano.ferreira@cnm.org.br>
 * $Id:$
 */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';
include_once ( CLA_PERSISTENTE );

class TCIMImovelFoto extends Persistente
{
    /**
        * Método Construtor
        * @access Private
    */
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('imobiliario.imovel_foto');

        $this->setCampoCod('cod_foto');
        $this->setComplementoChave('inscricao_municipal');

        $this->AddCampo('inscricao_municipal','integer',true,'',true ,true);
        $this->AddCampo('cod_foto'           ,'integer',true,'',true ,false);
        $this->AddCampo('descricao'          ,'text'   ,true,'',false,false);
        $this->AddCampo('foto'               ,'oid'   ,true,'',false,false);
    }

    public function recuperaFotosPorInscricao($inInscricao,&$rsFotos,$boTransacao='')
    {
        $stFiltro=' WHERE inscricao_municipal='.$inInscricao;
        $obErro = $this->recuperaTodos( $rsFotos,$stFiltro,'cod_foto',$boTransacao);

        return $obErro;
    }

    public function recuperaFoto(&$stImagem, $boTransacao = "")
    {
        $obErro      = new Erro;
        $obConexao   = new Conexao;
        $rsRecordSet = new RecordSet;
        $obErro = $this->recuperaPorChave( $rsRecordSet );
        if (!$obErro->ocorreu()) {
            if (Sessao::getTrataExcecao()) {
                $transacao = Sessao::getTransacao();
                $objeto = pg_lo_open($transacao->getConnection() ,  $rsRecordSet->getCampo('foto'), "r");
            } else {
                $objeto = pg_lo_open($obConexao->getConnection() ,  $rsRecordSet->getCampo('foto'), "r");
            }
            $stImagem =imagecreatefromstring(pg_lo_read($objeto,1000*1024));
        }

        return $obErro;
    }
}
