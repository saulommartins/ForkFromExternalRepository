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
* Classe de Mapeamento para a tabela sw_assunto_atributo
* Data de Criação: 01/09/2006

* @author Analista: Cassiano de Vasconcellos Ferreira
* @author Desenvolvedor: Cassiano de Vasconcellos Ferreira

$Revision: 15582 $
$Name$
$Author: cassiano $
$Date: 2006-09-18 08:38:09 -0300 (Seg, 18 Set 2006) $

Casos de uso: uc-01.06.93
*/
require_once CLA_PERSISTENTE;

class TPROAssuntoAtributo extends Persistente
{
    public function __construct()
    {
        parent::Persistente();
        $this->setTabela('sw_assunto_atributo');
        $this->setComplementoChave('cod_atributo,cod_classificacao,cod_assunto');

        $this->AddCampo('cod_atributo'     ,'integer',true,'',false,'TPROAtributoProtocolo');
        $this->AddCampo('cod_assunto'      ,'integer',true,'',false,'TPROAssunto');
        $this->AddCampo('cod_classificacao','integer',true,'',false,'TPROAssunto');
    }

    function validaExclusao($stFiltro = '', $boTransacao = '')
    {
        $obErro = new Erro();
        include_once(CAM_GA_PROT_MAPEAMENTO."TPROAssuntoAtributoValor.class.php");
        $obTPROAssuntoAtributoValor = new TPROAssuntoAtributoValor();
        $stFiltro  = ' WHERE cod_atributo='.$this->getDado('cod_atributo').' AND ';
        $stFiltro .= ' cod_classificacao='.$this->getDado('cod_classificacao').' AND ';
        $stFiltro .= ' cod_assunto='.$this->getDado('cod_assunto');
        $obErro = $obTPROAssuntoAtributoValor->recuperaTodos($rsAtributoValor,$stFiltro);
        if ( !$rsAtributoValor->eof() ) {
            include_once(CAM_GA_PROT_MAPEAMENTO."TPROAtributoProtocolo.class.php");
            $obTPROAtributoProtocolo = new TPROAtributoProtocolo();
            $obTPROAtributoProtocolo->setDado('cod_atributo', $this->getDado('cod_atributo'));
            $obTPROAtributoProtocolo->consultar();
            $obErro->setDescricao('O atributo '.$obTPROAtributoProtocolo->getDado('nom_atributo').' do assunto selecionado não pode ser excluído, porque está relacionado a um ou mais processos!');
            if ( Sessao::read('boTrataExcecao') ) {
                Sessao::getExcecao()->setDescricao($obErro->getDescricao());
            }
        }

        return $obErro;
    }

}
