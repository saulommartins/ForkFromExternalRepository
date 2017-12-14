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
    * Pagina oculta para emissão do documento da Ata
    * Data de Criação: 23/01/2009
    *
    *
    * @author Analista:      Gelson Wolowski Gonçalvez <gelson.goncalves@cnm.org.br>
    * @author Desenvolvedor: Diogo Zarpelon            <diogo.zarpelon@cnm.org.br>
    *
    * @ignore

    $Id: OCGeraManterAta.php 59612 2014-09-02 12:00:51Z gelson $

    */

include_once '../../../../../../gestaoAdministrativa/fontes/PHP/pacotes/FrameworkHTML.inc.php';
include_once '../../../../../../gestaoAdministrativa/fontes/PHP/framework/include/valida.inc.php';

include_once CAM_GA_ADM_MAPEAMENTO."TAdministracaoConfiguracao.class.php";

include_once CAM_GP_LIC_MAPEAMENTO.'TLicitacaoAta.class.php';
include_once TLIC."TLicitacaoComissaoLicitacao.class.php";
include_once TLIC."TLicitacaoComissaoMembros.class.php";
include_once TLIC."TLicitacaoMembroAdicional.class.php";
include_once TLIC."TLicitacaoParticipante.class.php";

include_once CAM_OOPARSER.'tbs_class.php';
include_once CAM_OOPARSER.'tbsooo_class.php';

$obTConfiguracao = new TAdministracaoConfiguracao;
$obTConfiguracao->setComplementoChave('exercicio, cod_modulo, parametro');
$obTConfiguracao->setDado('exercicio'  , Sessao::getExercicio());
$obTConfiguracao->setDado('cod_modulo' , 8);
$obTConfiguracao->setDado('parametro'  , 'cod_entidade_camara');
$obTConfiguracao->recuperaPorChave($rsConfiguracao);

$stFiltro = ' and M.cod_uf = '. $arConfiguracao['cod_uf'] ;
$obErro = $obTConfiguracao->recuperaMunicipio($rsMunicipio, $stFiltro );

$municipio = strtoupper($rsMunicipio->getCampo('nom_municipio'));
$estado    = $rsMunicipio->getCampo('nom_uf');

# Recupera dados da Ata.
$inIdAta = $request->get('inIdAta');
$obTAta = new TLicitacaoAta;
$obTAta->setDado('id' , $inIdAta);
$obTAta->recuperaPorChave($rsAta);

# Recupera dados do Edital.
$obTLicitacaoEdital = new TLicitacaoEdital;
$obTLicitacaoEdital->setDado('num_edital', $rsAta->getCampo('num_edital'));
$obTLicitacaoEdital->setDado('exercicio' , $rsAta->getCampo('exercicio') );
$obTLicitacaoEdital->recuperaPorChave($rsLicitacaoEdital);

# Dados da Licitação.
$inCodLicitacao       = $rsLicitacaoEdital->getCampo('cod_licitacao');
$stExercicioLicitacao = $rsLicitacaoEdital->getCampo('exercicio_licitacao');
$inCodEntidade        = $rsLicitacaoEdital->getCampo('cod_entidade');
$inCodModalidade      = $rsLicitacaoEdital->getCampo('cod_modalidade');

# Modalidade
$stModalidade = SistemaLegado::pegaDado('descricao', 'compras.modalidade', 'WHERE cod_modalidade = '.$inCodModalidade);

# Licitação
$stLicitacao = $inCodLicitacao.'/'.$stExercicioLicitacao;

# Poder (Executivo ou Legislativo)
$inIdCamara = $rsConfiguracao->getCampo('valor');
$tipo_poder = ($inCodEntidade == $inIdCamara) ? 'LEGISLATIVO' : 'EXECUTIVO';

# Recupera a comissão da Licitação
$obTLicitacaoComissaoLicitacao = new TLicitacaoComissaoLicitacao;
$obTLicitacaoComissaoLicitacao->setDado('cod_licitacao',$inCodLicitacao);
$obTLicitacaoComissaoLicitacao->setDado('cod_licitacao'  , $inCodLicitacao);
$obTLicitacaoComissaoLicitacao->setDado('cod_entidade'   , $inCodEntidade);
$obTLicitacaoComissaoLicitacao->setDado('exercicio'      , $stExercicioLicitacao);
$obTLicitacaoComissaoLicitacao->setDado('cod_modalidade' , $inCodModalidade);
$obTLicitacaoComissaoLicitacao->recuperaComissaoLicitacao($rsComissaoLicitacao);

# Recupera os membros da comissão
$obTLicitacaoComissaoMembros = new TLicitacaoComissaoMembros;
$obTLicitacaoComissaoMembros->recuperaComissaoLicitacaoMembrosPorComissao($rsMembros, $rsComissaoLicitacao->getCampo('cod_comissao'), $inCodModalidade, $inCodLicitacao);

# Membros Adicionais
$obTLicitacaoMembroAdicional = new TLicitacaoMembroAdicional;
$obTLicitacaoMembroAdicional->setDado('cod_licitacao'  , $inCodLicitacao);
$obTLicitacaoMembroAdicional->setDado('cod_entidade'   , $inCodEntidade);
$obTLicitacaoMembroAdicional->setDado('exercicio'      , $stExercicioLicitacao);
$obTLicitacaoMembroAdicional->setDado('cod_modalidade' , $inCodModalidade);
$obTLicitacaoMembroAdicional->recuperaMembroAdicional($rsMembroAdicional);

$arMembros = array();

# Inicializa variável para que não dê erro caso o presidente não esteja configurado.
$presidente = '';
# Recupera o presidente da comissão e adiciona os membros ao documento.
while (!$rsMembros->eof()) {

    if ($rsMembros->getCampo('cod_tipo_membro') == 2) {
        $presidente = $rsMembros->getCampo('nom_cgm');
    } else {
        $arMembros[]['nom_cgm'] = $rsMembros->getCampo('nom_cgm');
    }

    $rsMembros->proximo();
}

# Adiciona os membros adicionais ao documento.
while (!$rsMembroAdicional->eof()) {

    $arMembros[]['nom_cgm'] = $rsMembroAdicional->getCampo('nom_cgm');

    $rsMembroAdicional->proximo();
}

# Recupera os participantes da Licitação.
$obTLicitacaoParticipante = new TLicitacaoParticipante;
$obTLicitacaoParticipante->setDado('cod_licitacao'  , $inCodLicitacao);
$obTLicitacaoParticipante->setDado('cod_modalidade' , $inCodModalidade);
$obTLicitacaoParticipante->setDado('cod_entidade'   , $inCodEntidade);
$obTLicitacaoParticipante->setDado('exercicio'      , $stExercicioLicitacao);
$obTLicitacaoParticipante->recuperaRelacionamento($rsParticipantes);

# Tipo Logradouro
$stFiltro        = "WHERE parametro = 'tipo_logradouro' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 2";
$tipo_logradouro = (SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro));

# Logradouro
$stFiltro   = "WHERE parametro = 'logradouro' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 2";
$logradouro = (SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro));

# Número
$stFiltro = "WHERE parametro = 'numero' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 2";
$numero   = (SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro));

# Cep
$stFiltro = "WHERE parametro = 'cep' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 2";
$cep      = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro);

# Fone_Fax
$stFiltro = "WHERE parametro = 'fone' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 2";
$fone     = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro);
$stFiltro = "WHERE parametro = 'fax' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 2";
$fax      = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro);
$fone_fax = $fone."/".$fax;

# Site
$stFiltro = "WHERE parametro = 'site' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 2";
$site     = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro);
if ($site != '') {
    $site = ' - '.$site;
}

# Brasão
$stFiltro = "WHERE parametro = 'logotipo' AND exercicio = '".Sessao::getExercicio()."' AND cod_modulo = 2";
$brasao = SistemaLegado::pegaDado('valor', 'administracao.configuracao', $stFiltro);
$url_logo .= URBEM_ROOT_URL."/gestaoAdministrativa/fontes/PHP/framework/temas/padrao/imagens/".$brasao;

### Prepara para gerar o Documento com base no TemplateAta.odt ###
$OOo = new clsTinyButStrongOOo;
$OOo->SetDataCharset('UTF8');
$OOo->SetZipBinary('zip');
$OOo->SetUnzipBinary('unzip');
$OOo->SetProcessDir('/tmp/');

# Create a new openoffice document from the template with an unique id
$OOo->NewDocFromTpl(CAM_GP_LIC_ANEXOS."processoLicitatorio/TemplateAta.odt");

$ata        = $rsAta->getCampo('num_ata').'/'.$rsAta->getCampo('exercicio_ata');
$edital     = $rsAta->getCampo('num_edital').'/'.$rsAta->getCampo('exercicio');
$modalidade = ($stModalidade.' Nº '.$stLicitacao);
$descricao  = ($rsAta->getCampo('descricao'));

# merge data with OOo file content.xml
$OOo->LoadXmlFromDoc('content.xml');

# Imagem do cabeçalho (brasão)
$OOo->MergeField('url_logo' , $url_logo);

# Dados do Cabeçalho
$OOo->MergeField('municipio'  , $municipio  );
$OOo->MergeField('tipo_poder' , $tipo_poder );
$OOo->MergeField('estado'     , $estado     );

# Dados da Ata
$OOo->MergeField('ata'        , $ata   	    );
$OOo->MergeField('edital'     , $edital	    );
$OOo->MergeField('modalidade' , $modalidade );

# Descrição da Ata.
$OOo->MergeField('descricao'  , $descricao );

# Presidente da Comissão
$OOo->MergeField('presidente'  , $presidente );

# Membros da Comissão.
$OOo->MergeBlock('blk1', $arMembros);

# Participantes
$OOo->MergeBlock('blk2', $rsParticipantes->arElementos);

# Rodapé
$OOo->MergeField('tipo_logradouro' , $tipo_logradouro );
$OOo->MergeField('logradouro'      , $logradouro	  );
$OOo->MergeField('numero'  		   , $numero 	      );
$OOo->MergeField('cep'  		   , $cep	          );
$OOo->MergeField('fone_fax'  	   , $fone_fax        );
$OOo->MergeField('site'            , $site            );

# Salva
$OOo->SaveXmlToDoc();

# Merge data with OOo file styles.xml
$OOo->LoadXmlFromDoc('styles.xml');
$OOo->SaveXmlToDoc();

# Display
header('Content-Type: '.$OOo->GetMimetypeDoc().' name=Ata.odt');
header('Content-Length: '.filesize($OOo->GetPathnameDoc()));
header('Content-Disposition: attachment; filename=Ata.odt');

$OOo->FlushDoc();
$OOo->RemoveDoc();
